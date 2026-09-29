#!/bin/bash
set -e

if [[ -z "$DEBUG" || "$DEBUG" == "0" ]]; then
  set +x  # disable debugging
fi

# UID:GID the web server & storage run as; defaults keep stock behavior
APP_UID="${APP_UID:-33}"
APP_GID="${APP_GID:-33}"
APP_USER="www-data"
[[ "$APP_UID" == '33' ]] || APP_USER=app

echo "Started under UID $(id -u) GID $(id -g). Target webserver UID:GID $APP_UID:$APP_GID."

# when the container runs as root, apache will drop privileges and run
# as APP_UID:APP_GID, we do the same for the storage setup
if [ "$EUID" -eq 0 ]; then
  # renumber the app user so group lookup works for setpriv
  # (no-op when already matching; -o allows duplicate IDs)
  [ "$(id -u "$APP_USER")" -eq "$APP_UID" ] || usermod -o -u "$APP_UID" "$APP_USER"
  [ "$(id -g "$APP_USER")" -eq "$APP_GID" ] || groupmod -o -g "$APP_GID" "$APP_USER"

  # reconfigure apache to drop to APP_UID:APP_GID
  export APACHE_RUN_USER="$APP_USER" APACHE_RUN_GROUP="$APP_USER"

  # make sure we have access to the storage volume
  chown -R "$APP_UID:$APP_GID" /storage
  # drop privileges and run setup
  setpriv --reuid="$APP_UID" --regid="$APP_GID" --init-groups /dokuwiki-scripts/storagesetup.sh

else
  # we are already running as unprivileged user, just run setup since we cannot
  # do much in this case
  if [[ "$(id -u)" != "$APP_UID" ]]; then
    echo "WARNING: container started as non-root! " \
      "The current UID $(id -u) != $APP_UID (requested app user)"
  fi
  /dokuwiki-scripts/storagesetup.sh
fi

# run [default] command
if [[ -n "$1" ]]; then
  exec "$@"
else
  exec docker-php-entrypoint apache2-foreground
fi
