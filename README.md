# twitter test
[![CircleCI](https://circleci.com/gh/danielhuang-030/twitter-test/tree/master.svg?style=svg)](https://circleci.com/gh/danielhuang-030/twitter-test/tree/master)

### Introduction
Simple implementation of the basic functions of twitter: including member login/logout, post, follow and like.

### Packages
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
 - [Laravel Horizon](http://localhost:12001/horizon/dashboard)
 - [Swagger UI](http://localhost:12001/api-docs/)

### API info

* login
  * POST /api/login
    * Content-Type: application/json
    * Accept: application/json
    * Request JSON
    ```
    {
      "email": "{{email}}",
      "password": "{{password}}"
    }
    ```
    * Response JSON
    ```
    {
      "name": "test001",
      "email": "test001@test.com",
      "email_verified_at": null,
      "created_at": "2020-07-31 15:54:28",
      "updated_at": "2020-07-31 15:54:28",
      "token": "{{token}}"
    }
    ```

* signup
  * POST /api/signup
    * Content-Type: application/json
    * Accept: application/json
    * Request JSON
    ```
    {
      "name": "test005",
      "email": "test005@test.com",
      "password": "aaaaaa",
      "password_confirmation": "aaaaaa"
    }
    ```
    * Response JSON
    ```
    {
      "message": "Successfully created user!"
    }
    ```

* logout
  * GET /api/logout
    * Authorization: Bearer {{token}}
    * Content-Type: application/json
    * Accept: application/json

* user info
  * GET /api/users/:id/info
    * Authorization: Bearer {{token}}
    * Content-Type: application/json
    * Accept: application/json

* following user list
  * GET /api/users/:id/following
    * Authorization: Bearer {{token}}
    * Content-Type: application/json
    * Accept: application/json

* followers user list
  * GET /api/users/:id/followers
    * Authorization: Bearer {{token}}
    * Content-Type: application/json
    * Accept: application/json

* liked post list
  * GET /api/users/:id/liked_posts
    * Authorization: Bearer {{token}}
    * Content-Type: application/json
    * Accept: application/json

* follow user
  * POST /api/following
    * Authorization: Bearer {{token}}
    * Content-Type: application/json
    * Accept: application/json
    * Request JSON
    ```
    {
      "user_id": 3,
    }
    ```
    * Response JSON
    ```
    {
      "message": "Successfully followed user!"
    }
    ```

* unfollow user
  * DELETE /api/following/:id
    * Authorization: Bearer {{token}}
    * Content-Type: application/json
    * Accept: application/json

* add post
  * POST /api/post
    * Authorization: Bearer {{token}}
    * Content-Type: application/json
    * Accept: application/json
    * Request JSON
    ```
    {
      "content": "test content"
    }
    ```

* get post
  * GET /api/post/:id
    * Authorization: Bearer {{token}}
    * Content-Type: application/json
    * Accept: application/json

* liked users
  * GET /api/post/:id/liked_users
    * Authorization: Bearer {{token}}
    * Content-Type: application/json
    * Accept: application/json

* edit post
  * PUT /api/post/:id
    * Authorization: Bearer {{token}}
    * Content-Type: application/json
    * Accept: application/json
    * Request JSON
    ```
    {
      "content": "test content updated!!!"
    }
    ```

* del post
  * DELETE /api/post/:id
    * Authorization: Bearer {{token}}
    * Content-Type: application/json
    * Accept: application/json

* like post
  * PATCH /api/post/:id/like
    * Authorization: Bearer {{token}}
    * Content-Type: application/json
    * Accept: application/json

* dislike post
  * PATCH /api/post/:id/dislike
    * Authorization: Bearer {{token}}
    * Content-Type: application/json
    * Accept: application/json

### Dockerized
- [README.md](https://github.com/danielhuang-030/twitter-test/blob/master/dockerize/README.md)