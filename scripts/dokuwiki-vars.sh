#!/bin/bash
# Defines common dokuwiki variables & utilities for scripts
# use by sourcing!

DOKU_PATH=/var/www/html
DOKU_TPL_PATH=/var/www/html/lib/tpl.core
DOKU_PLUGINS_PATH=/var/www/html/lib/plugins.core
DOKU_DUMMY_EMAIL_DOMAIN="ocw.local"

DOKU_FARM_DATA="/storage/farm"
# print-returns the specified farm animal's root path
function doku_farm() {
	echo -n "$DOKU_FARM_DATA/$1"
}

# builds a users.auth.php syntax line with admin privileges (print-returns it)
function doku_mk_admin() {
	local NAME="${1:-admin}" PASSWORD="$2" HASH
	local EMAIL="${3:-"$NAME@$DOKU_DUMMY_EMAIL_DOMAIN"}"
	[[ -n "$PASSWORD" ]] || { echo "No admin password given!"; exit 1; }
	HASH=$(echo -n "$PASSWORD" | mkpasswd -s -m sha256crypt)
	echo "$NAME:$HASH:$NAME:$EMAIL:admin,user"
}

