<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\CourseController;
use App\Http\Controllers\Student\RazorpayController;
use App\Http\Controllers\Student\ProductSelectionController;
use App\Http\Controllers\Student\AffiliateLinkController;
use App\Http\Controllers\Student\StudentController;

Route::middleware(['auth', 'role:Student|Admin'])->group(function () {

    // 1. Unpaid/New User Routes (Accessible without purchase)
    Route::get('/product-selection', [ProductSelectionController::class, 'index'])->name('student.product_selection');
    Route::post('/product-selection/apply-referral', [ProductSelectionController::class, 'applyReferral'])->name('student.apply_referral');

    Route::post('/payment/create/{courseId}', [RazorpayController::class, 'createOrder'])->name('razorpay.create');
    Route::post('/payment/verify', [RazorpayController::class, 'verifyPayment'])->name('razorpay.verify');

    // 2. Paid/Active Student Routes (Requires Purchase)
    Route::middleware(['check.purchase'])->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Courses List/Details
        Route::get('/courses', [CourseController::class, 'index'])->name('student.courses.index');
        Route::get('/courses/{id}', [CourseController::class, 'show'])->name('student.courses.show');

        // LMS / My Learning Routes (Imported from HEAD during merge)
        Route::prefix('student')->name('student.')->group(function () {
            Route::get('/my-courses', [StudentController::class, 'myCourses'])->name('my-courses');
            Route::get('/watch/{course}/{lesson?}', [StudentController::class, 'watch'])->name('watch');
            Route::post('/progress/update', [StudentController::class, 'updateProgress'])->name('progress.update');
        });

        // Affiliate Links
        Route::resource('/affiliate/links', AffiliateLinkController::class)->names('student.affiliate.links');

        // Coupon Module (Affiliate)
        Route::get('/coupons', [App\Http\Controllers\Student\CouponController::class, 'index'])->name('student.coupons.index');
        Route::get('/coupons/store', [App\Http\Controllers\Student\CouponController::class, 'store'])->name('student.coupons.store');
        Route::post('/coupons/purchase', [App\Http\Controllers\Student\CouponController::class, 'purchase'])->name('student.coupons.purchase');
        Route::post('/coupons/transfer', [App\Http\Controllers\Student\CouponController::class, 'transfer'])->name('student.coupons.transfer');
    });
});
