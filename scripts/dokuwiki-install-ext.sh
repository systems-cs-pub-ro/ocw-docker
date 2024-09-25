#!/bin/bash
# Install dokuwiki extensions (plugins & templates)
set -e
source /dokuwiki-vars.sh

git clone "https://github.com/samfisch/dokuwiki-template-arctic.git" \
	"${DOKU_TPL_PATH}/arctic"

# add farmer plugin
git clone "https://github.com/cosmocode/dokuwiki-plugin-farmer.git" \
	"${DOKU_PLUGINS_PATH}/farmer"

