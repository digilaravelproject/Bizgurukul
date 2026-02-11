<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BundleController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\KycController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\Admin\CouponPackageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:Admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // 1. Dashboard & Settings
        // 1. Dashboard & Settings
        Route::resource('coupon-packages', CouponPackageController::class);
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/stats', [App\Http\Controllers\Admin\DashboardController::class, 'stats'])->name('dashboard.stats');
        Route::post('/settings/update', [AdminController::class, 'updateSettings'])->name('settings.update');

        // 2. Category Management
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::post('/store', [CategoryController::class, 'store'])->name('store');
            Route::put('/update/{id}', [CategoryController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [CategoryController::class, 'destroy'])->name('delete');
        });

        // 3. LMS: Course Management
        Route::prefix('courses')->name('courses.')->group(function () {
            Route::get('/', [CourseController::class, 'index'])->name('index');
            Route::get('/create', [CourseController::class, 'create'])->name('create');
            Route::post('/store', [CourseController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [CourseController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [CourseController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [CourseController::class, 'destroy'])->name('delete');
            Route::get('/sub-categories/{id}', [CourseController::class, 'getSubCategories'])->name('subcategories');
            Route::delete('/lesson/{id}/delete', [CourseController::class, 'destroyLesson'])->name('lesson.delete');
            Route::delete('/resource/{id}/delete', [CourseController::class, 'destroyResource'])->name('resource.delete');
            Route::post('/{id}/lesson/store', [CourseController::class, 'storeLesson'])->name('lesson.store');
            Route::post('/{id}/resource/store', [CourseController::class, 'storeResource'])->name('resource.store');
        });

        // 4. LMS: Lesson Management (Independent Operations if needed)
        Route::prefix('lessons')->name('lessons.')->group(function () {
            Route::get('/all-lessons', [LessonController::class, 'allLessons'])->name('all');
            Route::get('/create/{course_id}', [LessonController::class, 'create'])->name('create');
            Route::post('/store', [LessonController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [LessonController::class, 'edit'])->name('edit');
            Route::delete('/delete/{id}', [LessonController::class, 'destroy'])->name('delete');
        });

        // 5. Video Processing
        Route::post('/lms/upload', [VideoController::class, 'uploadVideo'])->name('video.upload');
        Route::post('/api/video-progress', [VideoController::class, 'updateHeartbeat'])->name('video.progress');

        // 6. Bundle Management
        // Route::prefix('bundles')->name('bundles.')->group(function () {
        //     Route::get('/create', [CourseController::class, 'createBundle'])->name('create');
        //     Route::post('/store', [CourseController::class, 'storeBundle'])->name('store');
        //     Route::get('/{id}/edit', [CourseController::class, 'editBundle'])->name('edit');
        //     Route::delete('/{id}', [CourseController::class, 'deleteBundle'])->name('delete');
        // });
        Route::resource('bundles', BundleController::class);
        // 7. Coupons
        Route::controller(CouponController::class)->prefix('coupons')->name('coupons.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/store', 'store')->name('store');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

        // 8. User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::post('/store', [UserController::class, 'store'])->name('store');
            Route::get('/{id}/details', [UserController::class, 'show'])->name('show');
            Route::post('/update/{id}', [UserController::class, 'update'])->name('update');
            Route::post('/ban/{id}', [UserController::class, 'toggleBan'])->name('ban');
            Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('delete');
            Route::post('/restore/{id}', [UserController::class, 'restore'])->name('restore');
            Route::delete('/force-delete/{id}', [UserController::class, 'forceDelete'])->name('force.delete');
        });

        // 9. KYC Requests
        Route::prefix('kyc-requests')->name('kyc.')->group(function () {
            Route::get('/', [KycController::class, 'index'])->name('index');
            Route::get('/{id}', [KycController::class, 'show'])->name('show');
            Route::post('/{id}/status', [KycController::class, 'updateStatus'])->name('status');
        });

        // 10. Affiliate Management
        Route::prefix('affiliate')->name('affiliate.')->group(function () {
            // Rules Management
            Route::get('/rules', [App\Http\Controllers\Admin\CommissionRuleController::class, 'index'])->name('rules.index');
            Route::post('/rules/store', [App\Http\Controllers\Admin\CommissionRuleController::class, 'store'])->name('rules.store');
            Route::delete('/rules/delete/{id}', [App\Http\Controllers\Admin\CommissionRuleController::class, 'destroy'])->name('rules.delete');

            // History / Reports
            Route::get('/history', [App\Http\Controllers\Admin\AffiliateController::class, 'history'])->name('history');
            Route::post('/commission/{id}/pay', [App\Http\Controllers\Admin\AffiliateController::class, 'markAsPaid'])->name('commission.pay');
        });
    });
