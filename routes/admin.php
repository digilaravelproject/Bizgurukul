<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CommissionController;
use App\Http\Controllers\Admin\CourseController; // New Controller for LMS
use App\Http\Controllers\Admin\VideoController;

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
            Route::get('/create', [CourseController::class, 'create'])->name('create');   // New Page with Form
            Route::post('/store', [CourseController::class, 'store'])->name('store');     // Save & Redirect
            Route::get('/edit/{id}', [CourseController::class, 'edit'])->name('edit');    // Edit Page
            Route::delete('/delete/{id}', [CourseController::class, 'delete'])->name('delete');
        });

        // 4. LMS: Lesson Management (Ajax CRUD)
        Route::prefix('lessons')->name('lessons.')->group(function () {
            Route::get('/fetch/{course_id}', [CourseController::class, 'fetchLessons'])->name('fetch');
            Route::post('/store', [CourseController::class, 'storeLesson'])->name('store');
            Route::delete('/delete/{id}', [CourseController::class, 'deleteLesson'])->name('delete');
        });

        // Video Processing & Heartbeat
        Route::post('/lms/upload', [VideoController::class, 'uploadVideo'])->name('video.upload');
        Route::post('/api/video-progress', [VideoController::class, 'updateHeartbeat'])->name('video.progress');
    });
