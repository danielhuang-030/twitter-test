<?php

use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\FollowController;
use App\Http\Controllers\Api\v1\PostController;
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

// swagger
Route::get('docs', [ApiController::class, 'getJSON']);

// v1
Route::prefix('v1')->group(function () {
    // withourt auth
    Route::post('login', [AuthController::class, 'login']);
    Route::post('signup', [AuthController::class, 'signup']);
    Route::get('posts', [PostController::class, 'index']);

    Route::group([
        'middleware' => 'auth:api',
    ], function () {
        // auth
        Route::get('logout', [AuthController::class, 'logout']);

        // profile
        Route::get('profile', [UserController::class, 'profile']);

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
});
