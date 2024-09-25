ARG VERSION=stable

FROM dokuwiki/dokuwiki:${VERSION} AS origin
FROM origin

ARG UNAME=app UID=1000 GID=1000

LABEL maintainer="eduard.c.staniloiu@gmail.com" \
      name="UPB OCW Dokuwiki"

# Install additional packages
RUN apt-get update && \
    apt-get install -y wget vim whois git rsync

COPY --chmod=0644 ./scripts/dokuwiki-vars.sh /dokuwiki-vars.sh
COPY --chmod=0755 ./scripts/dokuwiki-install-ext.sh /dokuwiki-install-ext.sh
RUN /dokuwiki-install-ext.sh

# create the default user
RUN groupadd -g "${GID}" "${UNAME}" && \
    useradd -l -m -u "${UID}" -g "${GID}" -s /bin/bash "${UNAME}"

# Install dokuwiki files with local config and farms config
COPY ./conf/dokuwiki/conf/local.php /var/www/html/conf.core/local.php
COPY ./conf/dokuwiki/conf/acl.auth.php /var/www/html/conf.core/acl.auth.php
COPY ./conf/dokuwiki/conf/users.auth.php /var/www/html/conf.core/users.auth.php
COPY ./conf/dokuwiki/_animal_defaults/ /var/www/html/conf.core/_animal_defaults/

COPY ./conf/dokuwiki/conf/conf.farm-baseurl.php /var/www/html/conf.core/conf.farm-baseurl.php
COPY ./conf/dokuwiki/preload.append.php /var/www/html/inc/preload.append.php
RUN cat /var/www/html/inc/preload.append.php >> /var/www/html/inc/preload.php

# Override storage setup entrypoint with our own:
COPY --from=origin /dokuwiki-storagesetup.sh /dokuwiki-storagesetup-orig.sh
COPY --chmod=0755 ./scripts/dokuwiki-storagesetup.sh /dokuwiki-storagesetup.sh
COPY --chmod=0755 ./scripts/doku-farm-new.sh /usr/local/bin/doku-farm-new
COPY --chmod=0755 ./scripts/doku-mkadmin.sh /usr/local/bin/doku-mkadmin

# Install apache config
# COPY ["./conf/apache2/sites-available/ocw-new.cs.pub.ro.conf", "/etc/apache2/sites-available/ocw-new.cs.pub.ro.conf"]
# RUN a2dissite 000-default.conf && a2ensite ocw-new.cs.pub.ro

# override the default htaccess (we use custom rewrite scheme)
COPY ./conf/dokuwiki/htaccess /var/www/html/.htaccess

