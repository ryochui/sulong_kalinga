<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\FamilyMemberController;
use App\Http\Controllers\CareWorkerController;
use App\Http\Controllers\WeeklyCareController;
use App\Http\Controllers\MunicipalityController;
use App\Http\Controllers\ExportController;

// All routes with care_worker role check
Route::middleware(['auth', 'role:care_worker'])->group(function () {
    // Dashboard
    Route::get('/worker/dashboard', function () {
        $showWelcome = session()->pull('show_welcome', false);
        return view('careWorker.workerdashboard', ['showWelcome' => $showWelcome]);
    })->name('workerdashboard');
    
    // Beneficiary Management - READ ONLY
    Route::get('/worker/beneficiaryProfile', [BeneficiaryController::class, 'index'])
        ->name('worker.beneficiaryProfile');
    Route::post('/worker/viewProfileDetails', [BeneficiaryController::class, 'viewProfileDetails'])
        ->name('worker.viewProfileDetails');
    
    // Family Member Management - READ ONLY
    Route::get('/worker/familyProfile', [FamilyMemberController::class, 'index'])
        ->name('worker.familyProfile');
    Route::post('/worker/viewFamilyDetails', [FamilyMemberController::class, 'viewFamilyDetails'])
        ->name('worker.viewFamilyDetails');
    
    // Weekly Care Plans - FULL ACCESS (primary responsibility)
    Route::get('/worker/weeklyCareplan', function () {
        return view('careWorker.weeklyCareplan');
    })->name('weeklyCareplan');
    
    Route::get('/worker/viewWeeklyCareplan', function () {
        return view('careWorker.viewWeeklyCareplan');
    })->name('viewWeeklyCareplan');
    
    Route::get('/worker/weekly-care-plans', [WeeklyCareController::class, 'index'])
        ->name('worker.weeklycareplans.index');
    Route::get('/worker/weekly-care-plan', [WeeklyCareController::class, 'create'])
        ->name('worker.weeklycareplans.create');
    Route::post('/worker/weekly-care-plan/store', [WeeklyCareController::class, 'store'])
        ->name('worker.weeklycareplans.store');
    Route::get('/worker/weekly-care-plans/{id}', [WeeklyCareController::class, 'show'])
        ->name('worker.weeklycareplans.show');
    Route::get('/worker/weekly-care-plans/{id}/edit', [WeeklyCareController::class, 'edit'])
        ->name('worker.weeklycareplans.edit');
    Route::put('/worker/weekly-care-plans/{id}', [WeeklyCareController::class, 'update'])
        ->name('worker.weeklycareplans.update');
    Route::get('/worker/weekly-care-plan/beneficiary/{id}', [WeeklyCareController::class, 'getBeneficiaryDetails'])
        ->name('worker.weeklycareplans.beneficiaryDetails');
    
    // Reports - Limited to their own reports
    Route::get('/worker/reports', [ReportsController::class, 'index'])
        ->name('worker.reports');
    
    // Municipality - READ ONLY
    Route::get('/worker/municipality', [MunicipalityController::class, 'index'])
        ->name('worker.municipality');
    
    // Export functionality - Limited to their own reports
    Route::post('/worker/export/weekly-care-pdf', [ExportController::class, 'exportWeeklyCareToPdf'])
        ->name('worker.export.weeklycare.pdf');
    Route::post('/worker/export/weekly-care-excel', [ExportController::class, 'exportWeeklyCareToExcel'])
        ->name('worker.export.weeklycare.excel');
    
    // Care Worker Profile - Only their own profile
    Route::get('/worker/profile', [CareWorkerController::class, 'viewOwnProfile'])
        ->name('worker.profile');
    Route::get('/worker/profile/edit', [CareWorkerController::class, 'editOwnProfile'])
        ->name('worker.profile.edit');
    Route::put('/worker/profile/update', [CareWorkerController::class, 'updateOwnProfile'])
        ->name('worker.profile.update');
    Route::post('/worker/profile/change-password', [CareWorkerController::class, 'changePassword'])
        ->name('worker.profile.changePassword');
});