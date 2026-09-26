#!/bin/bash
set -e

# first, run the original storage
/dokuwiki-scripts/storagesetup-orig.sh
source /dokuwiki-scripts/vars.sh

# additionally...
DOKU_CONF_CORE="$DOKU_PATH/conf.core"

# core configuration templates copied over to the storage
CONF_TEMPLATES=("local.php" "farm.ini" "acl.auth.php")
for conf_file in "${CONF_TEMPLATES[@]}"; do
	[[ -f "/storage/conf/$conf_file" ]] || \
		cp -f "$DOKU_CONF_CORE/$conf_file" "/storage/conf/$conf_file"
done

# replace specific container env. variables inside configs
sed -i 's|farmhost\s*=.*"|farmhost = "'"$DOKUWIKI_BASE_HOST"'"|' \
	"/storage/conf/farm.ini"

[[ -f "/storage/conf/users.auth.php" ]] || {
	cp "$DOKU_CONF_CORE/users.auth.php" "/storage/conf/users.auth.php"
	if [[ -n "$DOKUWIKI_ADMIN_PASSWORD" ]]; then
		doku_mk_admin "$DOKUWIKI_ADMIN_USER" "$DOKUWIKI_ADMIN_PASSWORD" >> \
			"/storage/conf/users.auth.php"
	fi
}

# add initial animal defaults to storage
[[ -e "/storage/_animal_defaults/" ]] || \
	rsync -rl --mkpath "$DOKU_CONF_CORE/_animal_defaults/" "/storage/_animal_defaults/"

