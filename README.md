# twitter test

### Installation

```shell
# git clone
git clone https://github.com/danielhuang-030/twitter_test.git

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
  * POST /api/auth/login
    * Content-Type: application/json
    * JSON
```
{
	"email": "[email]",
	"password": "[password]"
}
```

* signup
  * POST /api/auth/signup
    * Content-Type: application/json
    * JSON
```
{
	"name": "test005",
	"email": "test005@test.com",
	"password": "aaaaaaaa",
	"password_confirmation": "aaaaaaaa"
}
```

* user info
  * GET /api/user/info
    * Authorization: Bearer [api_token]

* follow user list
  * GET /api/user/follow
    * Authorization: Bearer [api_token]

* follow me user list
  * GET /api/user/follow_me
    * Authorization: Bearer [api_token]

* my liked post list
  * GET /api/user/liked_posts
    * Authorization: Bearer [api_token]

* follow user
  * POST /api/follow/[user_id]
    * Authorization: Bearer [api_token]

* unfollow user
  * DELETE /api/follow/[user_id]
    * Authorization: Bearer [api_token]

* add post
  * POST /api/post
    * Authorization: Bearer [api_token]
    * Content-Type: application/json
    * JSON
```
{
	"content": "test content"
}
```

* get post
  * GET /api/post/[post_id]
    * Authorization: Bearer [api_token]

* liked users
  * PATCH /api/post/[post_id]/liked_users
    * Authorization: Bearer [api_token]

* edit post
  * PATCH /api/post/[post_id]
    * Authorization: Bearer [api_token]
    * Content-Type: application/json
    * JSON
```
{
	"content": "test content updated!!!"
}
```

* del post
  * DELETE /api/post/[post_id]
    * Authorization: Bearer [api_token]

* like post
  * PATCH /api/post/[post_id]/like
    * Authorization: Bearer [api_token]

* dislike post
  * PATCH /api/post/[post_id]/dislike
    * Authorization: Bearer [api_token]