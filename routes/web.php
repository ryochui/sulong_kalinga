<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportsController;
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
use App\Http\Controllers\WeeklyCareController;

use App\Http\Middleware\CheckRole;


require __DIR__.'/innerRoutes.php';
// require __DIR__.'/careManagerRoutes.php';


// ROLE LOGIN
Route::get('/manager/dashboard', function () {
    if (auth()->user()?->isCareManager()) {
        return view('careManager.managerdashboard');
    }
    abort(403);
})->middleware('auth')->name('managerdashboard');

Route::get('/admin/dashboard', function () {
    if (auth()->user()?->isExecutiveDirector()) {
        return view('admin.admindashboard');
    }
    abort(403);
})->middleware('auth')->name('admindashboard');

Route::get('/worker/dashboard', function () {
    if (auth()->user()?->isCareWorker()) {
        return view('careWorker.workerdashboard');
    }
    abort(403);
})->middleware('auth')->name('workerdashboard');





// // Route for showing the login form (GET request)
// Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');

// // Route for handling the login form submission (POST request)
// Route::post('login', [AuthController::class, 'login'])->name('login.post');

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout'); // no logout for now

// Route::middleware('auth')->group(function () {
    
    Route::post('addAdministrator', [AdminController::class, 'storeAdministrator'])->name('admin.addAdministrator.store');
    Route::post('addCareManager', [CareManagerController::class, 'storeCareManager'])->name('admin.addCareManager.store');
    Route::post('addCareWorker', [CareWorkerController::class, 'storeCareWorker'])->name('admin.addCareWorker.store');
    Route::post('addFamily', [FamilyMemberController::class, 'storeFamily'])->name('admin.addFamily.store');
    Route::post('addBeneficiary', [BeneficiaryController::class, 'storeBeneficiary'])->name('admin.addBeneficiary.store');
    
    // Route::put('editAdminProfile/{id}', [AdminController::class, 'updateAdministrator'])->name('admin.editAdministrator.update'); // Replaced by below
    Route::put('/editAdminProfile/{id}', [AdminController::class, 'updateAdministrator'])->name('admin.editAdministrator.update');
    Route::get('/editAdminProfile/{id}', [AdminController::class, 'editAdminProfile'])->name('admin.editAdminProfile.edit');
    Route::get('/administratorProfile', [AdminController::class, 'index'])->name('administratorProfile'); // Keep
    
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

    // Route::get('editAdminProfile/{id}', [AdminController::class, 'editAdminProfile'])->name('admin.editAdminProfile');  // Not Keep
    
    

    // Route::get('/admin/addCareManager', function () {
    //     return view('admin.addCareManager');
    // })->name('admin.addCareManager');

    Route::get('addCareManager', [CareManagerController::class, 'create'])->name('admin.addCareManager');
    
    Route::get('addCareworker', [CareWorkerController::class, 'create'])->name('admin.addCareworker');

    Route::get('addFamily', [FamilyMemberController::class, 'create'])->name('admin.addFamily');

    Route::get('addBeneficiary', [BeneficiaryController::class, 'create'])->name('admin.addBeneficiary');

    //For beneficiary profiles table
    Route::get('/beneficiaryProfile', [BeneficiaryController::class, 'index'])->name('admin.beneficiaryProfile');
    Route::put('/admin/beneficiaries/{id}/status', [BeneficiaryController::class, 'updateStatus']);
    Route::put('/admin/beneficiaries/{id}/activate', [BeneficiaryController::class, 'activate']);
    Route::post('/validate-password', [UserController::class, 'validatePassword']);

    //For family member profiles table
    Route::get('/familyProfile', [FamilyMemberController::class, 'index'])->name('admin.familyProfile');    
    // For careworker profiles table
    Route::get('/careWorkerProfile', [CareWorkerController::class, 'index'])->name('admin.careWorkerProfile');
    Route::put('/admin/careworkers/{id}/status', [CareWorkerController::class, 'updateStatus']);
   
    // For caremanager profiles table
    Route::get('/careManagerProfile', [CareManagerController::class, 'index'])->name('admin.careManagerProfile');
    Route::put('/admin/caremanagers/{id}/status', [CareManagerController::class, 'updateStatus']);

    // For admin profiles table
    // Route::get('/administratorProfile', [AdminController::class, 'index'])->name('admin.administratorProfile'); // Replaced by below
    // Route::get('/administratorProfile', [AdminController::class, 'index'])->name('administratorProfile'); // Duplicate
    Route::put('/admin/administrators/{id}/status', [AdminController::class, 'updateStatus']);

    // For exporting to pdf/excel files
    Route::post('/export/beneficiaries-pdf', [ExportController::class, 'exportBeneficiariesToPdf'])->name('export.beneficiaries.pdf');
    Route::post('/export/family-pdf', [ExportController::class, 'exportFamilyToPdf'])->name('export.family.pdf');
    Route::post('/export/careworkers-pdf', [ExportController::class, 'exportCareworkersToPdf'])->name('export.careworkers.pdf');
    Route::post('/export/caremanagers-pdf', [ExportController::class, 'exportCaremanagersToPdf'])->name('export.caremanagers.pdf');
    Route::post('/export/administrators-pdf', [ExportController::class, 'exportAdministratorsToPdf'])->name('export.administrators.pdf');
    Route::post('/export/beneficiaries-excel', [ExportController::class, 'exportBeneficiariesToExcel'])->name('export.beneficiaries.excel');
    Route::post('/export/family-excel', [ExportController::class, 'exportFamilyMembersToExcel'])->name('export.family.excel');
    Route::post('/export/caremanagers-excel', [ExportController::class, 'exportCareManagersToExcel'])->name('export.caremanagers.excel');
    Route::post('/export/careworkers-excel', [ExportController::class, 'exportCareworkersToExcel'])->name('export.careworkers.excel');
    Route::post('/export/administrators-excel', [ExportController::class, 'exportAdministratorsToExcel'])->name('export.administrators.excel');

   // For deletion of entities
   Route::post('/admin/delete-administrator', [AdminController::class, 'deleteAdministrator'])
    ->name('admin.deleteAdministrator')
    ->middleware(['auth']);

    Route::post('/admin/delete-caremanager', [AdminController::class, 'deleteCaremanager'])
    ->name('admin.deleteCaremanager')
    ->middleware(['auth']); 

    Route::post('/admin/delete-careworker', [AdminController::class, 'deleteCareworker'])->middleware(['auth']);
    Route::post('/caremanager/delete-careworker', [CareManagerController::class, 'deleteCareworker'])->middleware(['auth']);
    
    Route::post('/admin/delete-family-member', [AdminController::class, 'deleteFamilyMember'])->middleware(['auth']);
    Route::post('/caremanager/delete-family-member', [CareManagerController::class, 'deleteFamilyMember'])->middleware(['auth']);

    Route::post('/admin/delete-beneficiary', [AdminController::class, 'deleteBeneficiary'])->middleware(['auth']);
    Route::post('/caremanager/delete-beneficiary', [CareManagerController::class, 'deleteBeneficiary'])->middleware(['auth']);

    //Reports Management
    //Route::get('/reports', [ReportsController::class, 'index'])->name('reports')->middleware('auth');

    // Route for displaying the form to create a new weekly care plan
    Route::get('/weekly-care-plan', [WeeklyCareController::class, 'create'])->name('weeklycareplans.create');

    // Route for storing the new weekly care plan
    Route::post('/weekly-care-plan/store', [WeeklyCareController::class, 'store'])->name('weeklycareplans.store');

    // Route for fetching beneficiary details via AJAX
    Route::get('/weekly-care-plan/beneficiary/{id}', [WeeklyCareController::class, 'getBeneficiaryDetails'])->name('weeklycareplans.beneficiaryDetails');

    // Route for displaying the list of weekly care plans
    Route::get('/weekly-care-plans', [WeeklyCareController::class, 'index'])->name('weeklycareplans.index');

    // Existing routes for other views
    Route::get('/weeklyCareplan', function () {
        return view('careWorker.weeklyCareplan');
    })->name('weeklyCareplan');

    Route::get('/viewWeeklyCareplan', function () {
        return view('careWorker.viewWeeklyCareplan');
    })->name('viewWeeklyCareplan');

    Route::get('/reports', [App\Http\Controllers\ReportsController::class, 'index'])
    ->name('reports')  // Changed from 'reports.index' to 'reports'
    ->middleware(['auth']);
    
    // Route for viewing individual reports
    Route::get('/weekly-care-plans/{id}', [App\Http\Controllers\WeeklyCareController::class, 'show'])
    ->name('weeklycareplans.show')
    ->middleware(['auth', CheckRole::class.':administrator,care_manager,care_worker']);

    