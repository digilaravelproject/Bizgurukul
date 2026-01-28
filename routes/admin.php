<?php

use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CommissionController;
use App\Http\Controllers\Admin\CourseController; // New Controller for LMS
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\Admin\LessonController;

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
    
        // 3. LMS: Course Management (Ajax CRUD)
        Route::prefix('courses')->name('courses.')->group(function () {
            Route::get('/', [CourseController::class, 'index'])->name('index');         // Table List Page
            Route::get('/create/{id?}', [CourseController::class, 'create'])->name('create');
            Route::post('/store', [CourseController::class, 'store'])->name('store');     // Save & Redirect
            Route::get('/edit/{id}', [CourseController::class, 'edit'])->name('edit');    // Edit Page
            Route::delete('/delete/{id}', [CourseController::class, 'delete'])->name('delete');
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
    });
