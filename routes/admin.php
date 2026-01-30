<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommissionController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\KycController; // New Controller for LMS
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VideoController;
use Illuminate\Support\Facades\Route;

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
        //     Route::get('/', [CommissionController::class, 'index'])->name('index');
        //     Route::post('/{commission}/approve', [CommissionController::class, 'approve'])->name('approve');
        //     Route::post('/{commission}/reject', [CommissionController::class, 'reject'])->name('reject');
        // });

        // // 3. Category Management
        // Route::prefix('categories')->name('categories.')->group(function () {
        //     Route::get('/', [CategoryController::class, 'index'])->name('index');
        //     Route::post('/store', [CategoryController::class, 'store'])->name('store');
        //     Route::get('/sub-categories/{parentId}', [CategoryController::class, 'getSubCategories'])->name('sub');
        //     Route::put('/update/{id}', [CategoryController::class, 'update'])->name('update');
        //     Route::delete('/delete/{id}', [CategoryController::class, 'destroy'])->name('delete');
        // });
        Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::post('/store', [CategoryController::class, 'store'])->name('store');
    Route::put('/update/{id}', [CategoryController::class, 'update'])->name('update'); // PUT method
    Route::delete('/delete/{id}', [CategoryController::class, 'destroy'])->name('delete'); // DELETE method
});
        // 3. LMS: Course Management (Ajax CRUD)
        Route::prefix('courses')->name('courses.')->group(function () {
            Route::get('/', [CourseController::class, 'index'])->name('index');         // Table List Page
            Route::get('/create/{id?}', [CourseController::class, 'create'])->name('create');
            Route::post('/store', [CourseController::class, 'store'])->name('store');     // Save & Redirect
            Route::get('/edit/{id}', [CourseController::class, 'edit'])->name('edit');    // Edit Page
            Route::post('/update/{id}', [CourseController::class, 'update'])->name('update');    // Edit Page
            Route::delete('/delete/{id}', [CourseController::class, 'delete'])->name('delete');
            Route::post('/lesson/store', [CourseController::class, 'lessonStore'])->name('lesson.store');
            Route::post('/resource/store', [CourseController::class, 'resourceStore'])->name('resource.store');
            Route::post('/resource/update', [CourseController::class, 'resourceUpdate'])->name('resource.update');
        });

        // 4. LMS: Lesson Management (Ajax CRUD)
        Route::prefix('lessons')->name('lessons.')->group(function () {
            Route::get('/all-lessons', [LessonController::class, 'allLessons'])->name('all');
            Route::get('/create/{course_id}', [LessonController::class, 'create'])->name('create');
            Route::post('/store', [LessonController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [LessonController::class, 'edit'])->name('edit');
            Route::delete('/delete/{id}', [LessonController::class, 'destroy'])->name('delete');
        });

        // Video Processing & Heartbeat
        Route::post('/lms/upload', [VideoController::class, 'uploadVideo'])->name('video.upload');
        Route::post('/api/video-progress', [VideoController::class, 'updateHeartbeat'])->name('video.progress');

        // Bundle Management Routes
        Route::get('/bundles/create', [CourseController::class, 'createBundle'])->name('bundles.create');
        Route::post('/bundles/store', [CourseController::class, 'storeBundle'])->name('bundles.store');
        Route::get('/bundles/{id}/edit', [CourseController::class, 'editBundle'])->name('bundles.edit');
        Route::delete('/bundles/{id}', [CourseController::class, 'deleteBundle'])->name('bundles.delete');

        Route::controller(CouponController::class)->group(function () {
            Route::get('coupons', 'index')->name('coupons.index');
            Route::post('coupons/store', 'store')->name('coupons.store');
            Route::get('coupons/{id}/edit', 'edit')->name('coupons.edit');
            Route::delete('coupons/{id}', 'destroy')->name('coupons.destroy');
        });

        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::post('/store', [UserController::class, 'store'])->name('store');
            Route::get('/{id}/details', [UserController::class, 'show'])->name('show'); // Logic wahi rakhna jo pehle tha JSON wala
            Route::post('/update/{id}', [UserController::class, 'update'])->name('update');
            Route::post('/ban/{id}', [UserController::class, 'toggleBan'])->name('ban');
            Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('delete');

            // Trash Routes
            Route::post('/restore/{id}', [UserController::class, 'restore'])->name('restore');
            Route::delete('/force-delete/{id}', [UserController::class, 'forceDelete'])->name('force.delete');
        });
        Route::prefix('kyc-requests')->name('kyc.')->group(function () {
            Route::get('/', [KycController::class, 'index'])->name('index'); // Alag Page
            Route::get('/{id}', [KycController::class, 'show'])->name('show'); // Modal Data
            Route::post('/{id}/status', [KycController::class, 'updateStatus'])->name('status'); // Approve/Reject
        });
    });
