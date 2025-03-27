<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\FamilyMemberController;
use App\Http\Controllers\CareWorkerController;
use App\Http\Controllers\CareManagerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;


Route::middleware(['auth'])->prefix('manager')->name('manager.')->group(function () {
    // Dashboard
    Route::get('/manager/dashboard', function () {
        return auth()->user()->isCareManager()
            ? view('manager.managerdashboard')
            : abort(403);
    })->name('managerdashboard');

    // Beneficiary Management
    Route::prefix('beneficiaries')->group(function () {
        Route::get('/', [BeneficiaryController::class, 'index'])->name('beneficiaries.index');
        Route::post('/{id}/delete', [CareManagerController::class, 'deleteBeneficiary'])->name('beneficiaries.delete');
        // Add more beneficiary routes as needed
    });

    // Care Worker Management
    Route::prefix('careworkers')->group(function () {
        Route::get('/', [CareWorkerController::class, 'index'])->name('careworkers.index');
        Route::post('/{id}/status', [CareManagerController::class, 'updateStatus'])->name('careworkers.status');
        Route::post('/{id}/delete', [CareManagerController::class, 'deleteCareworker'])->name('careworkers.delete');
    });

    // Family Member Management
    Route::prefix('families')->group(function () {
        Route::get('/', [FamilyMemberController::class, 'index'])->name('families.index');
        Route::post('/{id}/delete', [CareManagerController::class, 'deleteFamilyMember'])->name('families.delete');
    });

    // Reports
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports');

    // Weekly Care Plans
    Route::prefix('weekly-care-plans')->group(function () {
        Route::get('/', [WeeklyCareController::class, 'index'])->name('weeklycareplans.index');
        Route::get('/create', [WeeklyCareController::class, 'create'])->name('weeklycareplans.create');
        Route::post('/store', [WeeklyCareController::class, 'store'])->name('weeklycareplans.store');
        Route::get('/beneficiary/{id}', [WeeklyCareController::class, 'getBeneficiaryDetails'])->name('weeklycareplans.beneficiaryDetails');
    });

    // Profile Management
    Route::prefix('profile')->group(function () {
        Route::get('/', [CareManagerController::class, 'index'])->name('profile');
        Route::post('/details', [CareManagerController::class, 'viewCaremanagerDetails'])->name('profile.details');
        Route::post('/edit', [CareManagerController::class, 'editCaremanagerProfile'])->name('profile.edit');
    });
});



// Route::get('/addBeneficiary', function () {
//     return view('admin.addBeneficiary');
// })->name('addBeneficiary');


// Route::get('/addFamily', function () {
//     return view('admin.addFamily');
// })->name('addFamily');


// Route::get('/municipality', function () {
//     return view('careManager.municipality');
// })->name('careManagerMunicipality');




/*Route::get('/viewProfileDetails', function () {
    return view('admin.viewProfileDetails');
})->name('viewProfileDetails');*/

/*Route::get('/editProfile', function () {
    return view('admin.editProfile');
})->name('editProfile');*/



// //View and Edit Beneficiary Profile from Table Routing
// Route::post('/viewProfileDetails', [BeneficiaryController::class, 'viewProfileDetails'])->name('viewProfileDetails');
// Route::post('/editProfile', [BeneficiaryController::class, 'editProfile'])->name('editProfile');

// //View and Edit Family Profile from Table Routing
// Route::post('/viewFamilyDetails', [FamilyMemberController::class, 'viewFamilyDetails'])->name('viewFamilyDetails');
// Route::post('/editFamilyProfile', [FamilyMemberController::class, 'editFamilyProfile'])->name('editFamilyProfile');

// //View and Edit Care Worker Profile from Table Routing
// Route::post('/viewCareworkerDetails', [CareWorkerController::class, 'viewCareworkerDetails'])->name('viewCareworkerDetails');
// Route::post('/editCareworkerProfile', [CareWorkerController::class, 'editCareworkerProfile'])->name('editCareworkerProfile');

// //View and Edit Care Manager Profile from Table Routing
// Route::post('/viewCaremanagerDetails', [CareManagerController::class, 'viewCaremanagerDetails'])->name('viewCaremanagerDetails');
// Route::post('/editCaremanagerProfile', [CareManagerController::class, 'editCaremanagerProfile'])->name('editCaremanagerProfile');

// //View and Edit Care Manager Profile from Table Routing
// Route::post('/viewAdminDetails', [AdminController::class, 'viewAdminDetails'])->name('viewAdminDetails');
// Route::post('/editAdminProfile', [AdminController::class, 'editAdminProfile'])->name('editAdminProfile');




/*Route::get('/viewFamilyDetails', function () {
    return view('admin.viewFamilyDetails');
})->name('viewFamilyDetails');*/

/*Route::get('/editFamilyProfile', function () {
    return view('admin.editFamilyProfile');
})->name('editFamilyProfile');*/

/*Route::get('/viewCareworkerDetails', function () {
    return view('admin.viewCareworkerDetails');
})->name('viewCareworkerDetails');*/

/*Route::get('/editCareworkerProfile', function () {
    return view('admin.editCareworkerProfile');
})->name('editCareworkerProfile');*/

/*Route::get('/viewCaremanagerDetails', function () {
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
})->name('editAdminProfile');*/