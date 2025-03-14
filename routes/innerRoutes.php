<?php

use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/reportsManagemet', function () {
    return view('reportsManagemet');
})->name('reportsManagemet');

Route::get('/beneficiaryProfile', function () {
    return view('beneficiaryProfile');
})->name('beneficiaryProfile');

Route::get('/familyProfile', function () {
    return view('familyProfile');
})->name('familyProfile');

Route::get('/caregiverProfile', function () {
    return view('caregiverProfile');
})->name('caregiverProfile');

Route::get('/careManagerProfile', function () {
    return view('careManagerProfile');
})->name('careManagerProfile');

Route::get('/administratorProfile', function () {
    return view('administratorProfile');
})->name('administratorProfile');

Route::get('/municipality', function () {
    return view('municipality');
})->name('municipality');
