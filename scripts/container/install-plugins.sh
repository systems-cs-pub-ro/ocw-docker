#!/bin/bash
# Install dokuwiki extensions (plugins & templates)
set -e
source /dokuwiki-scripts/vars.sh

# Arctic Template (deprecated, superseded by included arctic-cs)
# git clone "https://github.com/samfisch/dokuwiki-template-arctic.git" \
# 	"${DOKU_TPL_PATH}/arctic"

# add farmer plugin
git_clone_plugin --branch="master" "cosmocode/dokuwiki-plugin-farmer" farmer

# add HTML content plugins
git_clone_plugin --branch="master" "selfthinker/dokuwiki_plugin_wrap" wrap
git_clone_plugin --branch="main" "saschaleib/dokuwiki-plugin-adhoctags" adhoctags
git_clone_plugin --branch="main" "saschaleib/dokuwiki-plugin-adhocwrap" adhocwrap
git_clone_plugin --branch="main" "saschaleib/dokuwiki-plugin-adhoctables" adhoctables
git_clone_plugin --branch="master" "Chris--S/dokuwiki-plugin-iframe" iframe
git_clone_plugin --branch="master" "splitbrain/dokuwiki-plugin-vshare" vshare

# Enable <html> FIXME: dangerous! should migrate away from it...
git_clone_plugin --branch="main" "saggi-dw/dokuwiki-plugin-htmlok" htmlok

# Various content plugins
git_clone_plugin --branch="master" "hanche/dokuwiki_color_plugin" color
git_clone_plugin --branch="2023-08-18" "dokufreaks/plugin-comment" comment
git_clone_plugin --branch="master" "splitbrain/dokuwiki-plugin-dw2pdf" dw2pdf
git_clone_plugin --branch="master" "fangebee/dokuwiki-plugin-menu" menu
git_clone_plugin --branch="master" "dokufreaks/plugin-include" include
git_clone_plugin --branch="master" "samuelet/indexmenu" indexmenu
git_clone_plugin --branch="main" "Hsins/dokuwiki-plugin-katex" katex
git_clone_plugin --branch="master" "AnaelMobilia/dokuwiki-plugin-note" note
git_clone_plugin --branch="master" "dokufreaks/plugin-pagelist" pagelist
git_clone_plugin --branch="master" "dwp-forge/tablewidth" tablewidth
git_clone_plugin --branch="master" "https://git.mittelab.org/proj/ifauthex-dokuwiki-plugin.git" ifauthex

# git_clone_plugin --branch="main" "https://codeberg.org/gturri/hidden.git" hidden

