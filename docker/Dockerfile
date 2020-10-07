FROM php:7.4-apache
ARG VERSION=unknown

LABEL maintainer="eduard.c.staniloiu@gmail.com" \
      name="UPB OCW Dokuwiki" \
      version="${VERSION}"

WORKDIR /var/www/html

# Install missing packages
RUN apt-get update && \
    apt-get install -y wget vim

# Install php-ldap
ADD https://raw.githubusercontent.com/mlocati/docker-php-extension-installer/master/install-php-extensions /usr/local/bin/

RUN chmod uga+x /usr/local/bin/install-php-extensions && \
    install-php-extensions ldap

# Download and extract latest stable dokuwiki
RUN wget https://download.dokuwiki.org/src/dokuwiki/dokuwiki-stable.tgz && \
    tar -xvf dokuwiki-stable.tgz && \
    rm dokuwiki-stable.tgz && \
    mv dokuwiki-* dokuwiki
    # Remove install script; we'll place the install files manually
    #rm dokuwiki/install.php

# Install dokuwiki files with local config and farms config
#COPY ["./conf/dokuwiki/conf/local.php", "./dokuwiki/conf/local.php"]
#COPY ["./conf/dokuwiki/conf/dokuwiki.php", "./dokuwiki/conf/dokuwiki.php"]
COPY ["./conf/dokuwiki/inc/preload.php", "./dokuwiki/inc/preload.php"]

# Install apache config
COPY ["./conf/apache2/sites-available/ocw-new.cs.pub.ro.conf", "/etc/apache2/sites-available/ocw-new.cs.pub.ro.conf"]

# Change ownership
RUN chown -R www-data:www-data dokuwiki && \
    # Enable apache rewrite module
    a2dissite 000-default.conf && \
    a2ensite ocw-new.cs.pub.ro && \
    a2enmod rewrite
