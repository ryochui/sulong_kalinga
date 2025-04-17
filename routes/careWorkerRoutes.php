<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\FamilyMemberController;
use App\Http\Controllers\CareWorkerController;
use App\Http\Controllers\WeeklyCareController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\UserController;

// All routes with care_worker role check
Route::middleware(['auth', 'role:care_worker'])->prefix('care-worker')->name('care-worker.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [CareWorkerController::class, 'dashboard'])->name('dashboard');
    
    // Care Worker Profile
    Route::get('/profile', [CareWorkerController::class, 'profile'])->name('profile');
    Route::post('/update-profile', [CareWorkerController::class, 'updateProfile'])->name('updateProfile');
    
    // Assigned Beneficiaries - read only for most actions
    Route::get('/beneficiaries', [BeneficiaryController::class, 'indexForCareWorker'])->name('beneficiaries');
    Route::get('/beneficiary/{id}', [BeneficiaryController::class, 'showForCareWorker'])->name('showBeneficiary');
    Route::post('/view-beneficiary-details', [BeneficiaryController::class, 'viewProfileDetailsForCareWorker'])->name('viewBeneficiaryDetails');
    
    // Limited Family Member Access
    Route::get('/beneficiary/{id}/family-members', [FamilyMemberController::class, 'indexForCareWorker'])->name('familyMembers');
    Route::get('/family-member/{id}', [FamilyMemberController::class, 'showForCareWorker'])->name('showFamilyMember');
    
    // Weekly Care Plans - can create and edit for assigned beneficiaries
    Route::get('/weekly-care-plans', [WeeklyCareController::class, 'indexForCareWorker'])->name('weeklyCarePlans');
    Route::get('/weekly-care-plan/create', [WeeklyCareController::class, 'createForCareWorker'])->name('createWeeklyCarePlan');
    Route::post('/weekly-care-plan/store', [WeeklyCareController::class, 'storeForCareWorker'])->name('storeWeeklyCarePlan');
    Route::get('/weekly-care-plan/{id}', [WeeklyCareController::class, 'showForCareWorker'])->name('showWeeklyCarePlan');
    Route::get('/weekly-care-plan/{id}/edit', [WeeklyCareController::class, 'editForCareWorker'])->name('editWeeklyCarePlan');
    Route::put('/weekly-care-plan/{id}', [WeeklyCareController::class, 'updateForCareWorker'])->name('updateWeeklyCarePlan');
    Route::get('/weekly-care-plan/beneficiary/{id}', [WeeklyCareController::class, 'getBeneficiaryDetailsForCareWorker'])->name('beneficiaryDetails');
    
    // Interventions and Visits
    Route::get('/interventions', [WeeklyCareController::class, 'interventionsForCareWorker'])->name('interventions');
    Route::post('/intervention/complete/{id}', [WeeklyCareController::class, 'completeInterventionForCareWorker'])->name('completeIntervention');
    Route::post('/intervention/reschedule/{id}', [WeeklyCareController::class, 'rescheduleInterventionForCareWorker'])->name('rescheduleIntervention');
    Route::post('/record-visit', [WeeklyCareController::class, 'recordVisitForCareWorker'])->name('recordVisit');
    
    // Limited Reports
    Route::get('/reports', [ReportsController::class, 'indexForCareWorker'])->name('reports');
    Route::post('/validate-password', [UserController::class, 'validatePassword'])->name('validatePassword');
});