#!/bin/bash
set -e
# first, run the original storage
/dokuwiki-storagesetup-orig.sh
source /dokuwiki-vars.sh

# additionally...
DOKU_CONF_CORE="$DOKU_PATH/conf.core"

# link local.php configuration from our docker image
[[ -f "/storage/conf/local.php" ]] || \
	ln -s "$DOKU_CONF_CORE/local.php" /storage/conf/local.php

# copy initial ACLs (if not exists)
[[ -f "/storage/conf/acl.auth.php" ]] || \
	cp "$DOKU_CONF_CORE/acl.auth.php" "/storage/conf/acl.auth.php"

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

