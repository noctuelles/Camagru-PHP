ARG PHP_VERSION
FROM php:${PHP_VERSION}-fpm
LABEL authors="plouvel"

WORKDIR /var/www/html

RUN docker-php-ext-install mysqli pdo pdo_mysql

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/

RUN install-php-extensions xdebug

COPY conf.d/docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/