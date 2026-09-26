#!/bin/bash
# Open CourseWare dokuwiki demo installer

set -euo pipefail
source /dokuwiki-scripts/vars.sh

echo "Installing OCW demo..."

cp -f ./courses.php /storage/conf/courses.php
rsync -rl  ./pages/ /storage/data/pages/
rsync -rl  ./media/ /storage/data/media/

while read -r NAME; do
	echo "Installing demo animal: ${NAME}";
	doku-farm-new.sh "$NAME"
	ANIMAL_PATH=$(doku_farm "$NAME")
	# copy pages
	rsync -rl "./farm/$NAME/"{media,pages} "$ANIMAL_PATH/data/"

done < <(find ./farm/ -mindepth 1 -maxdepth 1 -type d -printf "%f\n")

