<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ReportsController;

require __DIR__.'/innerRoutes.php';

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

Route::get('/milestones', function () {
    return view('publicWeb.milestones');
})->name('milestones');

Route::get('/updates', function () {
    return view('publicWeb.updates');
})->name('updates');

Route::get('/events', function () {
    return view('publicWeb.events');
})->name('events');

Route::get('/forgot-password', function () {
    return view ('forgot-password');
})->name('forgotPass');

Route::get('/admin/reportsManagement', [ReportsController::class, 'index'])->name('admin.reportsManagement');
