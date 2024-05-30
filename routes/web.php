<?php

use Illuminate\Support\Facades\Route;

// API DOCS
Route::get('/api-docs', function () {
    return file_get_contents(public_path('api-docs/index.html'));
});

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
