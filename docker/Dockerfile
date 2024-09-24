ARG VERSION=stable

FROM dokuwiki/dokuwiki:${VERSION} AS origin
FROM origin

LABEL maintainer="eduard.c.staniloiu@gmail.com" \
      name="UPB OCW Dokuwiki"

# Install additional packages
RUN apt-get update && \
    apt-get install -y wget vim whois git

COPY --chmod=0755 ./scripts/dokuwiki-install-ext.sh /dokuwiki-install-ext.sh
RUN /dokuwiki-install-ext.sh

# Install dokuwiki files with local config and farms config
COPY ./conf/dokuwiki/conf/local.php /var/www/html/conf.core/local.php
COPY ./conf/dokuwiki/conf/acl.auth.php /var/www/html/conf.core/acl.auth.php
COPY ./conf/dokuwiki/conf/users.auth.php /var/www/html/conf.core/users.auth.php

# Override storage setup entrypoint with our own:
COPY --from=origin /dokuwiki-storagesetup.sh /dokuwiki-storagesetup-orig.sh
COPY --chmod=0755 ./scripts/dokuwiki-storagesetup.sh /dokuwiki-storagesetup.sh

# Install apache config
# COPY ["./conf/apache2/sites-available/ocw-new.cs.pub.ro.conf", "/etc/apache2/sites-available/ocw-new.cs.pub.ro.conf"]
# RUN a2dissite 000-default.conf && a2ensite ocw-new.cs.pub.ro

