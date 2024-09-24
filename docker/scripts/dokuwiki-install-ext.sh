#!/bin/bash
# Install dokuwiki extensions (plugins & templates)
set -e

git clone https://github.com/samfisch/dokuwiki-template-arctic.git \
	/var/www/html/lib/tpl.core/arctic

