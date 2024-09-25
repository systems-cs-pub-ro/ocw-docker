#!/bin/bash
# Prints a new admin users.auth.php line (with hashed password)

set -e
source /dokuwiki-vars.sh

[[ -n "$1" && -n "$PASSWORD" ]] || { echo "Syntax: \`PASSWORD=... doku-mkadmin NAME [EMAIL]\`" >&2; exit 1; }

# note: this is an internal bash function, so passing the password as arg. is ok
doku_mk_admin "$1" "$PASSWORD" "$2"

