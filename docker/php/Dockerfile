FROM php:8.5-fpm-alpine

RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-install pdo_pgsql

RUN printf 'expose_php = Off\ndisplay_errors = Off\nlog_errors = On\n' \
    > /usr/local/etc/php/conf.d/security.ini

WORKDIR /app
