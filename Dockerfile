ARG VERSION=stable

FROM dokuwiki/dokuwiki:${VERSION} AS origin
FROM origin

LABEL org.opencontainers.image.authors="eduard.c.staniloiu@gmail.com, me@niflo.ro"
LABEL org.opencontainers.image.title="DokuWiki Image for ocw.cs.pub.ro"

ARG UNAME=app
ARG UID=1000
ARG GID=1000

# Install additional packages
RUN apt-get update && \
    apt-get install -y wget vim whois git rsync

COPY --chmod=0755 ./scripts/container/ /dokuwiki-scripts/
RUN /dokuwiki-scripts/install-plugins.sh

# create the default user
RUN groupadd -g "${GID}" "${UNAME}" && \
    useradd -l -m -u "${UID}" -g "${GID}" -s /bin/bash "${UNAME}"

# Install dokuwiki files with local config and farms config
COPY ./conf/dokuwiki/conf/ /var/www/html/conf.core/
COPY ./conf/dokuwiki/_animal_defaults/ /var/www/html/conf.core/_animal_defaults/
COPY ./conf/templates/ /var/www/html/lib/tpl.core/

COPY ./conf/dokuwiki/conf/conf.farm-baseurl.php /var/www/html/conf.core/conf.farm-baseurl.php
COPY ./conf/dokuwiki/preload.append.php /var/www/html/inc/preload.append.php
RUN cat /var/www/html/inc/preload.append.php >> /var/www/html/inc/preload.php

# override storage setup entrypoint with our own:
COPY --from=origin /dokuwiki-storagesetup.sh /dokuwiki-scripts/storagesetup-orig.sh
# official dokuwiki scripts are sh!3t
RUN sed -i '/set -x/d' /dokuwiki-scripts/storagesetup-orig.sh
# copy utils to bin
COPY --chmod=0755 ./scripts/utils/ /usr/local/bin/
RUN rm -f /dokuwiki-entrypoint.sh /dokuwiki-storagesetup.sh

# add our custom plugins
COPY ./conf/dokuwiki/plugins.custom/ /var/www/html/plugins.core/

# Install apache config
# COPY ["./conf/apache2/sites-available/ocw-new.cs.pub.ro.conf", "/etc/apache2/sites-available/ocw-new.cs.pub.ro.conf"]
# RUN a2dissite 000-default.conf && a2ensite ocw-new.cs.pub.ro

# remove htaccess symlink
RUN rm -f /var/www/html/.htaccess
# override the default htaccess (we use custom rewrite scheme)
COPY ./conf/dokuwiki/htaccess /var/www/html/.htaccess

ENV DOKUWIKI_BASE_HOST="http://localhost:8080"

ENTRYPOINT ["/dokuwiki-scripts/entrypoint.sh"]

