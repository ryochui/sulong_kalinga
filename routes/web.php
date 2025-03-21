<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ReportsController;
// use App\Http\Controllers\AuthController; // old auth
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
// for retrieving beneficiaries table
use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FamilyMemberController;
use App\Http\Controllers\CareWorkerController;
use App\Http\Controllers\CareManagerController;
use App\Http\Controllers\MunicipalityController;


require __DIR__.'/innerRoutes.php';

// // Route for showing the login form (GET request)
// Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');

// // Route for handling the login form submission (POST request)
// Route::post('login', [AuthController::class, 'login'])->name('login.post');

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout'); // no logout for now

// Route::middleware('auth')->group(function () {
    

    Route::post('/admin/addAdministrator', [AdminController::class, 'storeAdministrator'])->name('admin.addAdministrator.store');
    Route::post('/admin/addCareManager', [CareManagerController::class, 'storeCareManager'])->name('admin.addCareManager.store');
    
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

    Route::get('/admin/addAdministrator', function () {
        return view('admin.addAdministrator');
    })->name('admin.addAdministrator');

    // Route::get('/admin/addCareManager', function () {
    //     return view('admin.addCareManager');
    // })->name('admin.addCareManager');

    Route::get('/admin/addCareManager', [CareManagerController::class, 'create'])->name('admin.addCareManager');

    //For beneficiary profiles table
    Route::get('/beneficiaryProfile', [BeneficiaryController::class, 'index'])->name('admin.beneficiaryProfile');
    Route::put('/admin/beneficiaries/{id}/status', [BeneficiaryController::class, 'updateStatus']);
    Route::put('/admin/beneficiaries/{id}/activate', [BeneficiaryController::class, 'activate']);
    Route::post('/validate-password', [UserController::class, 'validatePassword']);

    //For family member profiles table
    Route::get('/familyProfile', [FamilyMemberController::class, 'index'])->name('admin.familyProfile');
    Route::put('/admin/family-members/{id}/status', [FamilyMemberController::class, 'updateStatus']);
    Route::put('/admin/family-members/{id}/activate', [FamilyMemberController::class, 'activate']);

    // For careworker profiles table
    Route::get('/careWorkerProfile', [CareWorkerController::class, 'index'])->name('admin.careWorkerProfile');

    // For caremanager profiles table
    Route::get('/careManagerProfile', [CareManagerController::class, 'index'])->name('admin.careManagerProfile');

    // For admin profiles table
    Route::get('/administratorProfile', [AdminController::class, 'index'])->name('admin.administratorProfile');