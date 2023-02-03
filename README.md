# twitter test
[![CircleCI](https://circleci.com/gh/danielhuang-030/twitter-test/tree/master.svg?style=svg)](https://circleci.com/gh/danielhuang-030/twitter-test/tree/master)
[online Swagger UI demo](https://twitter-test.dh030001.ga/api-docs/)

### Introduction
Simple implementation of the basic functions of twitter: including member login/logout, post, follow and like.

### Packages
- [laravel/octane](https://github.com/laravel/octane) - Laravel Octane
- [laravel/passport](https://github.com/laravel/passport) - Laravel Passport
- [laravel/horizon](https://github.com/laravel/horizon) - Laravel Horizon
- [pusher/pusher-http-php](https://github.com/pusher/pusher-http-php) - Pusher Channels HTTP PHP Library
- [zircote/swagger-php](https://github.com/zircote/swagger-php) - swagger-php
- [swagger-api/swagger-ui](https://github.com/swagger-api/swagger-ui) - Swagger UI

### Installation

```shell
# git clone
git clone https://github.com/danielhuang-030/twitter-test.git

# composer install
composer install

# copy .env and setting db/redis
cp .env.example .env
vi .env

# modify folders permissions
chmod 777 -R storage
chmod 777 -R bootstrap/cache

# generate key
php artisan key:generate

# symbolic link
php artisan storage:link

# db migrate
php artisan migrate

# horizon install
php artisan horizon:install

# passport install
php artisan passport:install

# run horizon
php artisan horizon

```
### Tools
 - [Laravel Horizon](http://localhost:12000/horizon/dashboard)
 - [Swagger UI](http://localhost:12000/api-docs/)

### Dockerized
- [README.md](https://github.com/danielhuang-030/twitter-test/blob/master/dockerize/README.md)
