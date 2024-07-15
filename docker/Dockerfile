FROM php:8.3-apache

RUN set -eux \
    && apt-get update -y \
    && apt-get upgrade -y \
    && apt-get install -y git zip unzip libzip-dev vim libonig-dev

RUN cd /etc/apache2/mods-enabled \
    && ln -s ../mods-available/rewrite.load

ADD php.ini /usr/local/etc/php/
ADD 000-default.conf /etc/apache2/sites-enabled/

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /composer
ENV PATH $PATH:/composer/vendor/bin

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer self-update --2

WORKDIR /app
