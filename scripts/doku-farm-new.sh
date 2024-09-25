#!/bin/bash
# Creates multiple farm animals using the template from the farmer plugin.
# Must be called using the container's unprivileged user!

set -e
source /dokuwiki-vars.sh

[[ -n "$1" ]] || { echo "Syntax: doku-farm-new ANIMAL_NAMES..." >&2; exit 1; }

for NAME in "$@"; do
	ANIMAL_PATH=$(doku_farm "$NAME")
	# TODO: implement --force
	[[ ! -e "$ANIMAL_PATH" ]] || {
		echo "Animal path '$ANIMAL_PATH' already exists!"
		continue
	}

	mkdir -p "$ANIMAL_PATH"
	rsync -rl "$DOKU_PLUGINS_PATH/farmer/_animal/" "$ANIMAL_PATH/"

	# overlay extra template from data storage dir (if present)
	[[ ! -d "/storage/_animal_defaults/" ]] || \
		rsync -rl "/storage/_animal_defaults/" "$ANIMAL_PATH/"

	# append animal name to conf
	echo "\$conf['title'] = '$NAME';" >> "$ANIMAL_PATH/conf/local.php"

	echo "Farm animal $NAME successfully created at '$ANIMAL_PATH'!"
done

