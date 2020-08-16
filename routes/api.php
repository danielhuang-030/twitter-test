<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// swagger
Route::get('docs', 'ApiController@getJSON');

// auth
Route::post('login', 'AuthController@login');
Route::post('signup', 'AuthController@signup');

Route::group([
    'middleware' => 'auth:api',
], function () {
    // auth
    Route::get('logout', 'AuthController@logout');

    // user
    Route::group([
        'prefix' => 'users',
    ], function () {
        Route::get('{id}/info', 'UserController@info');
        Route::get('{id}/following', 'UserController@following');
        Route::get('{id}/followers', 'UserController@followers');
        Route::get('{id}/posts', 'UserController@posts');
        Route::get('{id}/liked_posts', 'UserController@likedPosts');
    });

    // follow
    Route::group([
        'prefix' => 'following',
    ], function () {
        Route::patch('{id}', 'FollowController@following');
        Route::delete('{id}', 'FollowController@unfollow');
    });

    // post
    Route::group([
        'prefix' => 'posts',
    ], function () {
        Route::patch('{id}/like', 'PostController@like');
        Route::delete('{id}/like', 'PostController@dislike');
        Route::get('{id}/liked_users', 'PostController@likedUsers');
    });
    Route::resource('posts', 'PostController', [
        'parameters' => ['posts' => 'id'],
    ])->only([
        'show',
        'store',
        'update',
        'destroy',
    ]);
});
