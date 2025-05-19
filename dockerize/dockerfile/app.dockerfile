FROM php:8.4-fpm-alpine

LABEL maintainer="danielhuang-030"

ARG REDIS_VERSION=6.2.0
ARG SWOOLE_VERSION=6.0.2
ARG TZ=Asia/Taipei

ENV TZ=$TZ
RUN echo $TZ > /etc/timezone

RUN apk update && apk upgrade && \
    apk add --no-cache bash git vim supervisor curl freetype libpng libjpeg-turbo freetype-dev libpng-dev libjpeg-turbo-dev libzip-dev libwebp-dev imagemagick imagemagick-dev build-base nodejs npm tzdata

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions

RUN install-php-extensions pdo_mysql bcmath pcntl mbstring sockets zip gd imagick redis-$REDIS_VERSION swoole-$SWOOLE_VERSION

RUN apk del --purge freetype-dev libpng-dev libjpeg-turbo-dev libzip-dev libwebp-dev imagemagick-dev build-base musl-dev g++ && \
    rm -rf /var/cache/apk/* /tmp/* /var/tmp/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./conf/cron/root /etc/crontabs/root
COPY ./conf/supervisord/supervisord.conf /etc/supervisord.conf

ENTRYPOINT ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf"]
