FROM php:8.5-fpm-alpine

RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-install pdo_pgsql

RUN echo 'expose_php = Off' > /usr/local/etc/php/conf.d/security.ini

WORKDIR /app
