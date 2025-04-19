<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\FamilyMemberController;
use App\Http\Controllers\WeeklyCareController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\UserController;

// All routes with care_worker role check
Route::middleware(['auth', '\App\Http\Middleware\CheckRole:care_worker'])->prefix('care-worker')->name('care-worker.')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        $showWelcome = session()->pull('show_welcome', false);
        return view('careWorker.workerdashboard', ['showWelcome' => $showWelcome]);
    })->name('dashboard');
    
    // Beneficiary Management (add, edit, view only - no delete or status change)
    Route::prefix('beneficiaries')->name('beneficiaries.')->group(function () {
        Route::get('/', [BeneficiaryController::class, 'index'])->name('index');
        Route::get('/add-beneficiary', [BeneficiaryController::class, 'create'])->name('create');
        Route::post('/add-beneficiary', [BeneficiaryController::class, 'storeBeneficiary'])->name('store');
        Route::get('/edit-beneficiary/{id}', [BeneficiaryController::class, 'editBeneficiary'])->name('edit');
        Route::put('/edit-beneficiary/{id}', [BeneficiaryController::class, 'updateBeneficiary'])->name('update');
        Route::post('/view-beneficiary-details', [BeneficiaryController::class, 'viewProfileDetails'])->name('view-details');
        Route::post('/view', [BeneficiaryController::class, 'viewBeneficiary'])->name('view');
    });
    
    // Family Member Management (view, add, edit for assigned beneficiaries only - no delete)
    Route::prefix('families')->name('families.')->group(function () {
        Route::get('/', [FamilyMemberController::class, 'index'])->name('index');
        Route::get('/add', [FamilyMemberController::class, 'create'])->name('create');
        Route::post('/store', [FamilyMemberController::class, 'storeFamily'])->name('store');
        Route::get('/{id}/edit', [FamilyMemberController::class, 'editFamilyMember'])->name('edit');
        Route::put('/{id}', [FamilyMemberController::class, 'updateFamilyMember'])->name('update');
        Route::post('/view-details', [FamilyMemberController::class, 'viewFamilyDetails'])->name('view');
    });
    
    // Weekly Care Plans (add, view and edit authored plans only - no delete)
    Route::prefix('weekly-care-plans')->name('weeklycareplans.')->group(function () {
        Route::get('/', [WeeklyCareController::class, 'index'])->name('index');
        Route::get('/create', [WeeklyCareController::class, 'create'])->name('create');
        Route::post('/store', [WeeklyCareController::class, 'store'])->name('store');
        Route::get('/{id}', [WeeklyCareController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [WeeklyCareController::class, 'edit'])->name('edit');
        Route::put('/{id}', [WeeklyCareController::class, 'update'])->name('update');
        Route::get('/beneficiary/{id}', [WeeklyCareController::class, 'getBeneficiaryDetails'])->name('beneficiary-details');
    });

    // Reports Management (only authored reports)
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports');
    
    // Password validation route
    Route::post('/validate-password', [UserController::class, 'validatePassword'])->name('validate-password');

    // Exports (only for beneficiaries and families)
    Route::prefix('exports')->name('exports.')->group(function () {
        Route::post('/beneficiaries-pdf', [ExportController::class, 'exportBeneficiariesToPdf'])->name('beneficiaries-pdf');
        Route::post('/family-pdf', [ExportController::class, 'exportFamilyToPdf'])->name('family-pdf');
        Route::post('/beneficiaries-excel', [ExportController::class, 'exportBeneficiariesToExcel'])->name('beneficiaries-excel');
        Route::post('/family-excel', [ExportController::class, 'exportFamilyMembersToExcel'])->name('family-excel');
    });
});