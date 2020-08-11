FROM php:7.3-fpm-alpine

LABEL maintainer=""

RUN apk update && apk upgrade && apk add bash git vim && \
  apk --update add supervisor

RUN docker-php-ext-install pdo_mysql bcmath pcntl mbstring

RUN apk add --no-cache freetype libpng libjpeg-turbo freetype-dev libpng-dev libjpeg-turbo-dev libzip-dev curl && \
              docker-php-ext-configure gd \
                --with-gd \
                --with-freetype-dir=/usr/include/ \
                --with-png-dir=/usr/include/ \
                --with-jpeg-dir=/usr/include/ \
                --with-zlib-dir=/usr && \
              NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1)

## 安裝PHP-GD
RUN docker-php-ext-install -j$(nproc) gd

## 安裝PHP-ZIP
RUN docker-php-ext-install -j$(nproc) zip

RUN rm /var/cache/apk/* && \
    mkdir -p /var/www

## 安裝composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./dockerize/conf/cron/root /etc/crontabs/root
COPY ./dockerize/conf/supervisord/supervisord.conf /etc/supervisord.conf

ENTRYPOINT ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf"]
