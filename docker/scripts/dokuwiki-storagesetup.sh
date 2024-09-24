#!/bin/bash
set -e

# first, run the original storage
/dokuwiki-storagesetup-orig.sh

# additionally...

# link local.php configuration from our docker image
[[ -f "/storage/conf/local.php" ]] || \
	ln -s /var/www/html/conf.core/local.php /storage/conf/local.php

# copy initial ACLs (if not exists)
[[ -f "/storage/conf/acl.auth.php" ]] || \
	cp "/var/www/html/conf.core/acl.auth.php" "/storage/conf/acl.auth.php"

[[ -f "/storage/conf/users.auth.php" ]] || {
	cp "/var/www/html/conf.core/users.auth.php" "/storage/conf/users.auth.php"
	if [[ -n "$DOKUWIKI_ADMIN_PASSWORD" ]]; then
		DOKUWIKI_ADMIN_USER=${DOKUWIKI_ADMIN_USER:-"admin"}
		DOKUWIKI_ADMIN_EMAIL=${DOKUWIKI_ADMIN_EMAIL:-"admin@admin.com"}
		_hash=$(echo -n "$DOKUWIKI_ADMIN_PASSWORD" | mkpasswd -s -m sha256crypt)
		echo "$DOKUWIKI_ADMIN_USER:$_hash:$DOKUWIKI_ADMIN_USER:$DOKUWIKI_ADMIN_EMAIL:admin,user" \
			>> "/storage/conf/users.auth.php"
	fi
}

