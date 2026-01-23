<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CommissionController;

/*
|--------------------------------------------------------------------------
| Admin Routes (Organized & Clean)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:Admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // 1. Dashboard & Settings
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::post('/settings/update', [AdminController::class, 'updateSettings'])->name('settings.update');

        // 2. Affiliate Commissions Management
        // Route::prefix('commissions')->name('commissions.')->group(function () {
        //     // Pending commissions list
        //     Route::get('/', [CommissionController::class, 'index'])->name('index');

        //     // Approve/Pay a commission
        //     Route::post('/{commission}/approve', [CommissionController::class, 'approve'])->name('approve');

        //     // Reject a commission (with reason)
        //     Route::post('/{commission}/reject', [CommissionController::class, 'reject'])->name('reject');
        // });

    });
