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
use App\Http\Controllers\ExportController;


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
    Route::post('/admin/addCareWorker', [CareWorkerController::class, 'storeCareWorker'])->name('admin.addCareWorker.store');
    Route::post('/admin/addFamily', [FamilyMemberController::class, 'storeFamily'])->name('admin.addFamily.store');
    
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
    
    // NO REPORTSCONTROLLER YET, UNCOMMENT WHEN READY
    // Route::get('reportsManagement', [ReportsController::class, 'index'])->name('admin.reportsManagement');

    Route::get('addAdministrator', function () {
        return view('admin.addAdministrator');
    })->name('admin.addAdministrator');

    // Route::get('/admin/addCareManager', function () {
    //     return view('admin.addCareManager');
    // })->name('admin.addCareManager');

    Route::get('addCareManager', [CareManagerController::class, 'create'])->name('admin.addCareManager');
    
    Route::get('addCareworker', [CareWorkerController::class, 'create'])->name('admin.addCareworker');

    Route::get('addFamily', [FamilyMemberController::class, 'create'])->name('admin.addFamily');

    //For beneficiary profiles table
    Route::get('/beneficiaryProfile', [BeneficiaryController::class, 'index'])->name('admin.beneficiaryProfile');
    Route::put('/admin/beneficiaries/{id}/status', [BeneficiaryController::class, 'updateStatus']);
    Route::put('/admin/beneficiaries/{id}/activate', [BeneficiaryController::class, 'activate']);
    Route::post('/validate-password', [UserController::class, 'validatePassword']);

    //For family member profiles table
    Route::get('/familyProfile', [FamilyMemberController::class, 'index'])->name('admin.familyProfile');
    Route::put('/admin/family-members/{id}/status', [FamilyMemberController::class, 'updateStatus']);
    
    // For careworker profiles table
    Route::get('/careWorkerProfile', [CareWorkerController::class, 'index'])->name('admin.careWorkerProfile');
    Route::put('/admin/careworkers/{id}/status', [CareWorkerController::class, 'updateStatus']);
   
    // For caremanager profiles table
    Route::get('/careManagerProfile', [CareManagerController::class, 'index'])->name('admin.careManagerProfile');
    Route::put('/admin/caremanagers/{id}/status', [CareManagerController::class, 'updateStatus']);

    // For admin profiles table
    Route::get('/administratorProfile', [AdminController::class, 'index'])->name('admin.administratorProfile');
    Route::put('/admin/administrators/{id}/status', [AdminController::class, 'updateStatus']);

    // For exporting to pdf/excel files
    Route::post('/export/beneficiaries-pdf', [ExportController::class, 'exportBeneficiariesToPdf'])->name('export.beneficiaries.pdf');
    Route::post('/export/family-pdf', [ExportController::class, 'exportFamilyToPdf'])->name('export.family.pdf');
    Route::post('/export/careworkers-pdf', [ExportController::class, 'exportCareworkersToPdf'])->name('export.careworkers.pdf');
    Route::post('/export/caremanagers-pdf', [ExportController::class, 'exportCaremanagersToPdf'])->name('export.caremanagers.pdf');
    Route::post('/export/administrators-pdf', [ExportController::class, 'exportAdministratorsToPdf'])->name('export.administrators.pdf');