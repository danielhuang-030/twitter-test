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

use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;

// swagger
Route::get('docs', [ApiController::class, 'getJSON']);

// auth
Route::post('login', [AuthController::class, 'login']);
Route::post('signup', [AuthController::class, 'signup']);

Route::group([
    'middleware' => 'auth:api',
], function () {
    // auth
    Route::get('logout', [AuthController::class, 'logout']);

    // user
    Route::controller(UserController::class)
        ->prefix('users')
        ->group(function () {
            Route::get('{id}/info', 'info');
            Route::get('{id}/following', 'following');
            Route::get('{id}/followers', 'followers');
            Route::get('{id}/posts', 'posts');
            Route::get('{id}/liked_posts', 'likedPosts');
        });

    // follow
    Route::controller(FollowController::class)
        ->prefix('following')
        ->group(function () {
            Route::patch('{id}', 'following');
            Route::delete('{id}', 'unfollow');
        });

    // post
    Route::controller(PostController::class)
        ->prefix('posts')
        ->group(function () {
            Route::patch('{id}/like', 'like');
            Route::delete('{id}/like', 'dislike');
            Route::get('{id}/liked_users', 'likedUsers');
        });
    Route::resource('posts', PostController::class, [
        'parameters' => ['posts' => 'id'],
    ])->only([
        'show',
        'store',
        'update',
        'destroy',
    ]);
});
