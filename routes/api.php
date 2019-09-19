<?php

use Illuminate\Http\Request;

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

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');
});

Route::group([
    'middleware' => 'auth:api'
], function () {
    // auth
    Route::group([
        'prefix' => 'auth'
    ], function () {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });

    // follow
    Route::group([
        'prefix' => 'follow'
    ], function () {
        Route::get('/', 'FollowController@index');
        Route::post('/{id}', 'FollowController@store');
        Route::delete('/{id}', 'FollowController@destroy');
    });

    // post
    Route::resource('post', 'PostController');

    // user
    Route::group([
        'prefix' => 'user'
    ], function () {
        Route::get('/info', 'UserController@info');
        Route::get('/follow', 'UserController@follow');
        Route::get('/follow_me', 'UserController@followMe');
    });
});
