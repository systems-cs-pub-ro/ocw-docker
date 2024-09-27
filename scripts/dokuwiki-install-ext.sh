#!/bin/bash
# Install dokuwiki extensions (plugins & templates)
set -e
source /dokuwiki-vars.sh

git clone "https://github.com/samfisch/dokuwiki-template-arctic.git" \
	"${DOKU_TPL_PATH}/arctic"

# add farmer plugin
git_clone_plugin --branch="2024-04-16" "cosmocode/dokuwiki-plugin-farmer" farmer

# add HTML content plugins
git_clone_plugin --branch="2023-10-14" "saschaleib/dokuwiki-plugin-adhoctags" adhoctags
git_clone_plugin --branch="2023-04-20" "saschaleib/dokuwiki-plugin-adhocwrap" adhocwrap
git_clone_plugin --branch="main" "saschaleib/dokuwiki-plugin-adhoctables" adhoctables
git_clone_plugin --branch="master" "Chris--S/dokuwiki-plugin-iframe" iframe
git_clone_plugin --branch="2024-03-24" "splitbrain/dokuwiki-plugin-vshare" vshare

# Enable <html> FIXME: dangerous! should migrate away from it...
git_clone_plugin --branch="main" "saggi-dw/dokuwiki-plugin-htmlok" htmlok

