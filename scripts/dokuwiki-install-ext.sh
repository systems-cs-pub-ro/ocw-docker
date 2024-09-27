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

# Various content plugins
git_clone_plugin --branch="master" "hanche/dokuwiki_color_plugin" color
git_clone_plugin --branch="v.2023-06-16" "dwp-forge/columns" columns
git_clone_plugin --branch="2023-08-18" "dokufreaks/plugin-comment" comment
git_clone_plugin --branch="2023-11-25" "splitbrain/dokuwiki-plugin-dw2pdf" dw2pdf
git_clone_plugin --branch="master" "Skywalker1-CZ/dokuwiki-dwmenu" dwmenu
git_clone_plugin --branch="2023-09-22" "dokufreaks/plugin-include" include
git_clone_plugin --branch="v2024-01-05" "samuelet/indexmenu" indexmenu
git_clone_plugin --branch="main" "Hsins/dokuwiki-plugin-katex" katex
git_clone_plugin --branch="master" "AnaelMobilia/dokuwiki-plugin-note" note
git_clone_plugin --branch="2023-08-27" "dokufreaks/plugin-pagelist" pagelist
git_clone_plugin --branch="v.2022-08-09" "dwp-forge/tablewidth" tablewidth
git_clone_plugin --branch="v0.3" "https://git.mittelab.org/proj/ifauthex-dokuwiki-plugin.git" ifauthex

