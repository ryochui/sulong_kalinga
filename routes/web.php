<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('publicWeb.landing');
})->name('landing');

Route::get('/login', function () {
    return view ('auth.login');
})->name('login');

Route::get('/contactUs', function () {
    return view('publicWeb.contactUs');
})->name('contactUs');

Route::get('/donor', function () {
    return view('publicWeb.donor');
})->name('donor');

Route::get('/aboutUs', function () {
    return view('publicWeb.aboutUs');
})->name('aboutUs');




Route::get('/forgot-password', function () {
    return view ('forgot-password');
})->name('forgotPass');