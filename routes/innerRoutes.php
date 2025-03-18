<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ReportsController;


Route::get('/dashboard', function () {
    return view('admin.dashboard');
})->name('dashboard');

Route::get('/reportsManagement', function () {
    return view('admin.reportsManagement');
})->name('reportsManagement');

Route::get('/beneficiaryProfile', function () {
    return view('admin.beneficiaryProfile');
})->name('beneficiaryProfile');

Route::get('/addBeneficiary', function () {
    return view('admin.addBeneficiary');
})->name('addBeneficiary');


Route::get('/familyProfile', function () {
    return view('admin.familyProfile');
})->name('familyProfile');

Route::get('/addFamily', function () {
    return view('admin.addFamily');
})->name('addFamily');

Route::get('/caregiverProfile', function () {
    return view('admin.caregiverProfile');
})->name('caregiverProfile');

Route::get('/addCareworker', function () {
    return view('admin.addCareworker');
})->name('addCareworker');

Route::get('/careManagerProfile', function () {
    return view('admin.careManagerProfile');
})->name('careManagerProfile');

Route::get('/addCareManager', function () {
    return view('admin.addCareManager');
})->name('addCareManager');

Route::get('/administratorProfile', function () {
    return view('admin.administratorProfile');
})->name('administratorProfile');

Route::get('/addAdministrator', function () {
    return view('admin.addAdministrator');
})->name('addAdministrator');


Route::get('/municipality', function () {
    return view('admin.municipality');
})->name('municipality');
