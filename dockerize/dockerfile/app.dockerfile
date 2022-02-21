FROM php:8.0-fpm-alpine

LABEL maintainer=""

RUN apk update && apk upgrade && apk add bash git vim && \
  apk --update add supervisor

RUN docker-php-ext-install pdo_mysql bcmath pcntl

RUN apk add --no-cache freetype libpng libjpeg-turbo freetype-dev libpng-dev libjpeg-turbo-dev libzip-dev libwebp-dev curl && \
  docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg --with-webp

## 安裝PHP-GD
RUN docker-php-ext-install gd

## 安裝PHP-ZIP
RUN docker-php-ext-install zip

RUN rm /var/cache/apk/* && \
    mkdir -p /var/www

## 安裝composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./dockerize/conf/cron/root /etc/crontabs/root
COPY ./dockerize/conf/supervisord/supervisord.conf /etc/supervisord.conf

ENTRYPOINT ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf"]
