# twitter test
[![CircleCI](https://circleci.com/gh/danielhuang-030/twitter-test/tree/master.svg?style=svg)](https://circleci.com/gh/danielhuang-030/twitter-test/tree/master)
- [online demo](https://twitter-test.333030.xyz/)
- [Swagger UI](https://twitter-test.333030.xyz/api-docs/)

### Introduction
Simple implementation of the basic functions of twitter: including member login/logout, post, follow and like. (Back-end only)

### Features
- Member registration, login, logout, and CRUD functions for posts
- Members can click like on posts or cancel
- Members can follow other members or be followed by other members
- Instant notification to members via websocket
  - Notify all followers when a member posts a new post
  - Notify members when the status of following is changed

### Tech Stack
- Using Laravel 12(PHP 8.4 with swoole) + MySQL 5.7 + Redis + soketi(websocket) + Vue 3

### Packages
- [laravel/octane](https://github.com/laravel/octane) - Laravel Octane
- [laravel/passport](https://github.com/laravel/passport) - Laravel Passport
- [laravel/horizon](https://github.com/laravel/horizon) - Laravel Horizon
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

# passport init
php artisan passport:keys
php artisan passport:client --personal --no-interaction

# run horizon
php artisan horizon

# npm install
npm install

# npm run production
npm run production

```
### Tools
 - [Laravel Horizon](http://localhost:12000/horizon/dashboard)
 - [Swagger UI](http://localhost:12000/api-docs/)

### Dockerized
- [README.md](https://github.com/danielhuang-030/twitter-test/blob/master/dockerize/README.md)
