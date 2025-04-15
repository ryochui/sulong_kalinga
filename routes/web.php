<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Middleware\CheckRole;

// Include route files for role-specific routes
require __DIR__.'/adminRoutes.php';
require __DIR__.'/careManagerRoutes.php';
require __DIR__.'/careWorkerRoutes.php';

// Authentication Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/forgot-password', function () {
    return view('forgot-password');
})->name('forgotPass');

// Dashboard routes by role
Route::get('/admin/dashboard', function () {
    if (auth()->user()?->isExecutiveDirector()) {
        $showWelcome = session()->pull('show_welcome', false);
        return view('admin.admindashboard', ['showWelcome' => $showWelcome]);
    }
    abort(403);
})->middleware('auth')->name('admindashboard');

Route::get('/manager/dashboard', function () {
    if (auth()->user()?->isCareManager()) {
        $showWelcome = session()->pull('show_welcome', false);
        return view('careManager.managerdashboard', ['showWelcome' => $showWelcome]);
    }
    abort(403);
})->middleware('auth')->name('managerdashboard');

Route::get('/worker/dashboard', function () {
    $showWelcome = session()->pull('show_welcome', false);
    if (auth()->user()?->isCareWorker()) {
        return view('careWorker.workerdashboard', ['showWelcome' => $showWelcome]);
    }
    abort(403);
})->middleware('auth')->name('workerdashboard');

// Public website routes
Route::get('/', function () {
    return view('publicWeb.landing');
})->name('landing');

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