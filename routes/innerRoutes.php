<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\BeneficiaryController;


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
    return view('admin.careWorkerProfile');
})->name('careWorkerProfile');

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

Route::get('/weeklyCareplan', function () {
    return view('careWorker.weeklyCareplan');
})->name('weeklyCareplan');

Route::get('/viewProfileDetails', function () {
    return view('admin.viewProfileDetails');
})->name('viewProfileDetails');

Route::get('/editProfile', function () {
    return view('admin.editProfile');
})->name('editProfile');

//View and Edit Beneficiary Profile from Table Routing
Route::post('/viewProfileDetails', [BeneficiaryController::class, 'viewProfileDetails'])->name('viewProfileDetails');
Route::post('/editProfile', [BeneficiaryController::class, 'editProfile'])->name('editProfile');

Route::get('/viewFamilyDetails', function () {
    return view('admin.viewFamilyDetails');
})->name('viewFamilyDetails');

Route::get('/editFamilyProfile', function () {
    return view('admin.editFamilyProfile');
})->name('editFamilyProfile');

Route::get('/viewCareworkerDetails', function () {
    return view('admin.viewCareworkerDetails');
})->name('viewCareworkerDetails');

Route::get('/editCareworkerProfile', function () {
    return view('admin.editCareworkerProfile');
})->name('editCareworkerProfile');

Route::get('/viewCaremanagerDetails', function () {
    return view('admin.viewCaremanagerDetails');
})->name('viewCaremanagerDetails');

Route::get('/editCaremanagerProfile', function () {
    return view('admin.editCaremanagerProfile');
})->name('editCaremanagerProfile');

Route::get('/viewAdminDetails', function () {
    return view('admin.viewAdminDetails');
})->name('viewAdminDetails');

Route::get('/editAdminProfile', function () {
    return view('admin.editAdminProfile');
})->name('editAdminProfile');