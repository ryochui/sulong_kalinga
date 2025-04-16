<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\FamilyMemberController;
use App\Http\Controllers\CareWorkerController;
use App\Http\Controllers\CareManagerController;
use App\Http\Controllers\WeeklyCareController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MunicipalityController;

// All routes with care_manager role check
Route::middleware(['auth', '\App\Http\Middleware\CheckRole:care_manager'])->prefix('care-manager')->name('care-manager.')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        $showWelcome = session()->pull('show_welcome', false);
        return view('careManager.managerdashboard', ['showWelcome' => $showWelcome]);
    })->name('dashboard');
    
    // Care Worker Management (limited)
    Route::get('/care-workers', [CareWorkerController::class, 'index'])->name('careWorkers');
    Route::get('/add-care-worker', [CareWorkerController::class, 'create'])->name('addCareWorker');
    Route::post('/add-care-worker', [CareWorkerController::class, 'store'])->name('storeCareWorker');
    Route::get('/care-worker/{id}', [CareWorkerController::class, 'show'])->name('showCareWorker');
    Route::get('/edit-care-worker/{id}', [CareWorkerController::class, 'edit'])->name('editCareWorker');
    Route::put('/edit-care-worker/{id}', [CareWorkerController::class, 'update'])->name('updateCareWorker');
    Route::put('/care-worker/{id}/status', [CareWorkerController::class, 'updateStatus'])->name('updateCareWorkerStatus');
    
    // Beneficiary Management
    Route::get('/beneficiaries', [BeneficiaryController::class, 'index'])->name('beneficiaries');
    Route::get('/add-beneficiary', [BeneficiaryController::class, 'create'])->name('addBeneficiary');
    Route::post('/add-beneficiary', [BeneficiaryController::class, 'store'])->name('storeBeneficiary');
    Route::get('/beneficiary/{id}', [BeneficiaryController::class, 'show'])->name('showBeneficiary');
    Route::get('/edit-beneficiary/{id}', [BeneficiaryController::class, 'edit'])->name('editBeneficiary');
    Route::put('/edit-beneficiary/{id}', [BeneficiaryController::class, 'update'])->name('updateBeneficiary');
    Route::put('/beneficiary/{id}/status', [BeneficiaryController::class, 'updateStatus'])->name('updateBeneficiaryStatus');
    Route::post('/view-beneficiary-details', [BeneficiaryController::class, 'viewProfileDetails'])->name('viewBeneficiaryDetails');
    
    // Family Member Management
    Route::get('/family-members', [FamilyMemberController::class, 'index'])->name('familyMembers');
    Route::get('/add-family-member', [FamilyMemberController::class, 'create'])->name('addFamilyMember');
    Route::post('/add-family-member', [FamilyMemberController::class, 'store'])->name('storeFamilyMember');
    Route::get('/family-member/{id}', [FamilyMemberController::class, 'show'])->name('showFamilyMember');
    Route::get('/edit-family-member/{id}', [FamilyMemberController::class, 'edit'])->name('editFamilyMember');
    Route::put('/edit-family-member/{id}', [FamilyMemberController::class, 'update'])->name('updateFamilyMember');
    Route::post('/view-family-details', [FamilyMemberController::class, 'viewFamilyDetails'])->name('viewFamilyDetails');
    
    // Weekly Care Plans
    Route::get('/weekly-care-plans', [WeeklyCareController::class, 'index'])->name('weeklyCarePlans');
    Route::get('/weekly-care-plan/create', [WeeklyCareController::class, 'create'])->name('createWeeklyCarePlan');
    Route::post('/weekly-care-plan/store', [WeeklyCareController::class, 'store'])->name('storeWeeklyCarePlan');
    Route::get('/weekly-care-plan/{id}', [WeeklyCareController::class, 'show'])->name('showWeeklyCarePlan');
    Route::get('/weekly-care-plan/{id}/edit', [WeeklyCareController::class, 'edit'])->name('editWeeklyCarePlan');
    Route::put('/weekly-care-plan/{id}', [WeeklyCareController::class, 'update'])->name('updateWeeklyCarePlan');
    Route::delete('/weekly-care-plan/{id}/delete', [WeeklyCareController::class, 'destroy'])->name('deleteWeeklyCarePlan');
    Route::get('/weekly-care-plan/beneficiary/{id}', [WeeklyCareController::class, 'getBeneficiaryDetails'])->name('beneficiaryDetails');
    
    // Reports
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports');
    Route::post('/validate-password', [UserController::class, 'validatePassword'])->name('validatePassword');
    
    // Exports - Limited for Care Manager
    Route::post('/export/beneficiaries-pdf', [ExportController::class, 'exportBeneficiariesToPdf'])->name('exportBeneficiariesPdf');
    Route::post('/export/family-pdf', [ExportController::class, 'exportFamilyToPdf'])->name('exportFamilyPdf');
    Route::post('/export/careworkers-pdf', [ExportController::class, 'exportCareworkersToPdf'])->name('exportCareworkersPdf');
    Route::post('/export/beneficiaries-excel', [ExportController::class, 'exportBeneficiariesToExcel'])->name('exportBeneficiariesExcel');
    Route::post('/export/family-excel', [ExportController::class, 'exportFamilyMembersToExcel'])->name('exportFamilyExcel');
    Route::post('/export/careworkers-excel', [ExportController::class, 'exportCareworkersToExcel'])->name('exportCareworkersExcel');
});