<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\CrawlerController;

// Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/crawler/index/{crawler}/{monitor}', [CrawlerController::class, 'index']);
Route::get('/crawler/checked/{crawler}/{monitor}', [CrawlerController::class, 'checked']);

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
