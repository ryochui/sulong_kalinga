<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\FamilyMemberController;
use App\Http\Controllers\CareWorkerController;
use App\Http\Controllers\CareManagerController;
use App\Http\Controllers\WeeklyCareController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MunicipalityController;

// All routes with administrator role check
Route::middleware(['auth', 'role:administrator'])->group(function () {
    // Dashboard route is already in web.php
    
    // Administrator Management
    Route::get('/administratorProfile', [AdminController::class, 'index'])->name('administratorProfile');
    Route::get('/addAdministrator', function () {
        return view('admin.addAdministrator');
    })->name('admin.addAdministrator');
    Route::post('/addAdministrator', [AdminController::class, 'storeAdministrator'])->name('admin.addAdministrator.store');
    Route::get('/editAdminProfile/{id}', [AdminController::class, 'editAdminProfile'])->name('admin.editAdminProfile.edit');
    Route::put('/editAdminProfile/{id}', [AdminController::class, 'updateAdministrator'])->name('admin.editAdministrator.update');
    Route::put('/admin/administrators/{id}/status', [AdminController::class, 'updateStatus']);
    Route::post('/admin/delete-administrator', [AdminController::class, 'deleteAdministrator'])->name('admin.deleteAdministrator');
    Route::post('/viewAdminDetails', [AdminController::class, 'viewAdminDetails'])->name('viewAdminDetails');

    // Care Manager Management
    Route::get('/careManagerProfile', [CareManagerController::class, 'index'])->name('admin.careManagerProfile');
    Route::get('/addCareManager', [CareManagerController::class, 'create'])->name('admin.addCareManager');
    Route::post('/addCareManager', [CareManagerController::class, 'storeCareManager'])->name('admin.addCareManager.store');
    Route::get('/editCaremanagerProfile/{id}', [CareManagerController::class, 'editCaremanagerProfile'])->name('admin.editCaremanagerProfile.edit');
    Route::put('/editCaremanagerProfile/{id}', [CareManagerController::class, 'updateCaremanager'])->name('admin.editCaremanager.update');
    Route::put('/admin/caremanagers/{id}/status', [CareManagerController::class, 'updateStatus']);
    Route::post('/admin/delete-caremanager', [AdminController::class, 'deleteCaremanager'])->name('admin.deleteCaremanager');
    Route::post('/viewCaremanagerDetails', [CareManagerController::class, 'viewCaremanagerDetails'])->name('viewCaremanagerDetails');

    // Care Worker Management
    Route::get('/careWorkerProfile', [CareWorkerController::class, 'index'])->name('admin.careWorkerProfile');
    Route::get('/addCareworker', [CareWorkerController::class, 'create'])->name('admin.addCareWorker.create');
    Route::post('/addCareworker', [CareWorkerController::class, 'storeCareWorker'])->name('admin.addCareWorker');
    Route::put('/admin/careworkers/{id}/status', [CareWorkerController::class, 'updateStatus']);
    Route::post('/admin/delete-careworker', [AdminController::class, 'deleteCareworker']);
    Route::post('/viewCareworkerDetails', [CareWorkerController::class, 'viewCareworkerDetails'])->name('viewCareworkerDetails');
    
    // Beneficiary Management
    Route::get('/beneficiaryProfile', [BeneficiaryController::class, 'index'])->name('admin.beneficiaryProfile');
    Route::get('/addBeneficiary', [BeneficiaryController::class, 'create'])->name('admin.addBeneficiary');
    Route::post('/addBeneficiary', [BeneficiaryController::class, 'storeBeneficiary'])->name('admin.addBeneficiary.store');
    Route::put('/admin/beneficiaries/{id}/status', [BeneficiaryController::class, 'updateStatus']);
    Route::put('/admin/beneficiaries/{id}/activate', [BeneficiaryController::class, 'activate']);
    Route::post('/admin/delete-beneficiary', [AdminController::class, 'deleteBeneficiary']);
    Route::post('/viewProfileDetails', [BeneficiaryController::class, 'viewProfileDetails'])->name('viewProfileDetails');
    Route::post('/editProfile', [BeneficiaryController::class, 'editProfile'])->name('editProfile');

    // Family Member Management
    Route::get('/familyProfile', [FamilyMemberController::class, 'index'])->name('admin.familyProfile');
    Route::get('/addFamily', [FamilyMemberController::class, 'create'])->name('admin.addFamily');
    Route::post('/addFamily', [FamilyMemberController::class, 'storeFamily'])->name('admin.addFamily.store');
    Route::post('/admin/delete-family-member', [AdminController::class, 'deleteFamilyMember']);
    Route::post('/viewFamilyDetails', [FamilyMemberController::class, 'viewFamilyDetails'])->name('viewFamilyDetails');
    Route::post('/editFamilyProfile', [FamilyMemberController::class, 'editFamilyProfile'])->name('editFamilyProfile');

    // Municipality Management
    Route::get('/municipality', [AdminController::class, 'municipality'])->name('municipality');
    Route::delete('/admin/delete-barangay/{id}', [AdminController::class, 'deleteBarangay'])->name('admin.deleteBarangay');
    Route::delete('/admin/delete-municipality/{id}', [AdminController::class, 'deleteMunicipality'])->name('admin.deleteMunicipality');
    Route::post('/admin/add-municipality', [AdminController::class, 'addMunicipality'])->name('admin.addMunicipality');
    Route::post('/admin/add-barangay', [AdminController::class, 'addBarangay'])->name('admin.addBarangay');
    Route::post('/admin/update-municipality', [AdminController::class, 'updateMunicipality'])->name('admin.updateMunicipality');
    Route::post('/admin/update-barangay', [AdminController::class, 'updateBarangay'])->name('admin.updateBarangay');
    
    // Reports and Exports
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports');
    Route::post('/validate-password', [UserController::class, 'validatePassword']);
    
    // Export Routes
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
    
    // Weekly Care Plans access for admin
    Route::get('/weekly-care-plans', [WeeklyCareController::class, 'index'])->name('weeklycareplans.index');
    Route::get('/weekly-care-plan', [WeeklyCareController::class, 'create'])->name('weeklycareplans.create');
    Route::post('/weekly-care-plan/store', [WeeklyCareController::class, 'store'])->name('weeklycareplans.store');
    Route::get('/weekly-care-plan/beneficiary/{id}', [WeeklyCareController::class, 'getBeneficiaryDetails'])->name('weeklycareplans.beneficiaryDetails');
    Route::get('/weekly-care-plans/{id}', [WeeklyCareController::class, 'show'])->name('weeklycareplans.show');
});