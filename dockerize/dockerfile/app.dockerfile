FROM php:8.1-fpm-alpine

LABEL maintainer=""

ARG TZ=Asia/Taipei
ENV TZ=${TZ}
RUN echo $TZ > /etc/timezone

RUN apk update && apk upgrade && apk add bash git vim libxml2-dev oniguruma-dev tzdata && \
  apk --update add supervisor nodejs npm

RUN docker-php-ext-install pdo_mysql bcmath pcntl mbstring sockets

RUN apk add --no-cache freetype libpng libjpeg-turbo freetype-dev libpng-dev libjpeg-turbo-dev libzip-dev libwebp-dev curl && \
  docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg --with-webp

## 安裝PHP-GD
RUN docker-php-ext-install gd

## 安裝PHP-ZIP
RUN docker-php-ext-install zip

## install redis, swoole
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS
RUN pecl install -o -f redis \
    && pecl install swoole \
	&& rm -rf /tmp/pear \
	&& docker-php-ext-enable redis swoole

RUN rm /var/cache/apk/* && \
    mkdir -p /var/www && \
	  mkdir -m 777 -p /var/log/supervisor

## 安裝composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./conf/cron/root /etc/crontabs/root
COPY ./conf/supervisord/supervisord.conf /etc/supervisord.conf

ENTRYPOINT ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf"]
