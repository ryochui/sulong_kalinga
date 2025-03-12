<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::get('/login', function () {
    return view ('login');
})->name('login');

Route::get('/forgot-password', function () {
    return view ('forgot-password');
})->name('forgotPass');