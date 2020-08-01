# twitter test

### Introduction
Simple implementation of the basic functions of twitter: including member login/logout, post, follow and like.

### Packages
- [laravel/passport](https://github.com/laravel/passport) - Laravel Passport
- [laravel/horizon](https://github.com/laravel/horizon) - Laravel Horizon

### Installation

```shell
# git clone
git clone https://github.com/danielhuang-030/twitter-test.git

# composer install
composer install

# copy .env and setting db
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

### API info

* login
  * POST /api/login
    * Content-Type: application/json
    * Accept: application/json
    * Request JSON
    ```
    {
      "email": "[email]",
      "password": "[password]"
    }
    ```
    * Response JSON
    ```
    {
      "token": "[token]"
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
      "password": "aaaaaaaa",
      "password_confirmation": "aaaaaaaa"
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
    * Authorization: Bearer [token]
    * Content-Type: application/json
    * Accept: application/json

* self user info
  * GET /api/users/:id/info
    * Authorization: Bearer [token]
    * Content-Type: application/json
    * Accept: application/json

* following user list
  * GET /api/users/:id/following
    * Authorization: Bearer [token]
    * Content-Type: application/json
    * Accept: application/json

* followers list
  * GET /api/users/:id/followers
    * Authorization: Bearer [token]
    * Content-Type: application/json
    * Accept: application/json

* liked post list
  * GET /api/users/:id/liked_posts
    * Authorization: Bearer [token]
    * Content-Type: application/json
    * Accept: application/json

* follow user
  * POST /api/following
    * Authorization: Bearer [token]
    * Content-Type: application/json
    * Accept: application/json
    * Request JSON
    ```
    {
      "name": "test005",
      "email": "test005@test.com",
      "password": "aaaaaaaa",
      "password_confirmation": "aaaaaaaa"
    }
    ```
    * Response JSON
    ```
    {
      "message": "Successfully followed user!"
    }
    ```

* unfollow user
  * DELETE /api/following/[user_id]
    * Authorization: Bearer [token]
    * Content-Type: application/json
    * Accept: application/json

* add post
  * POST /api/post
    * Authorization: Bearer [token]
    * Content-Type: application/json
    * Accept: application/json
    * Request JSON
    ```
    {
      "content": "test content"
    }
    ```

* get post
  * GET /api/post/[post_id]
    * Authorization: Bearer [token]
    * Content-Type: application/json
    * Accept: application/json

* liked users
  * PATCH /api/post/[post_id]/liked_users
    * Authorization: Bearer [token]
    * Content-Type: application/json
    * Accept: application/json

* edit post
  * PATCH /api/post/[post_id]
    * Authorization: Bearer [token]
    * Content-Type: application/json
    * Accept: application/json
    * Request JSON
    ```
    {
      "content": "test content updated!!!"
    }
    ```

* del post
  * DELETE /api/post/[post_id]
    * Authorization: Bearer [token]
    * Content-Type: application/json
    * Accept: application/json

* like post
  * PATCH /api/post/[post_id]/like
    * Authorization: Bearer [token]
    * Content-Type: application/json
    * Accept: application/json

* dislike post
  * PATCH /api/post/[post_id]/dislike
    * Authorization: Bearer [token]
    * Content-Type: application/json
    * Accept: application/json