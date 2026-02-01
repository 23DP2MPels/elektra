<?php

use Illuminate\Support\Facades\Route;

// SPA: отдаём app для любых путей, кроме /api
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '^(?!api).*');
