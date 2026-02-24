<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\CourseController;
use App\Http\Controllers\Student\RazorpayController;
use App\Http\Controllers\Student\ProductSelectionController;
use App\Http\Controllers\Student\AffiliateLinkController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Student\CouponController;
use App\Http\Controllers\Student\ProfileController;
use App\Http\Controllers\Student\AffiliateController;
use App\Http\Controllers\Student\InvoiceController;

/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
*/

// Accessible by Students and Admins (e.g., product selection)
Route::middleware(['auth', 'role:Student|Admin'])->group(function () {

    // 1. New/Unpaid User Flow
    Route::get('/product-selection', [ProductSelectionController::class, 'index'])->name('student.product_selection');
    Route::post('/product-selection/apply-referral', [ProductSelectionController::class, 'applyReferral'])->name('student.apply_referral');

    // 2. Student Course Purchase Payment
    Route::get('/checkout/{type}/{id}', [RazorpayController::class, 'checkout'])->name('student.checkout');
    Route::post('/payment/create/{type}/{id}', [RazorpayController::class, 'createOrder'])->name('razorpay.create');
    Route::post('/student/payment/verify', [RazorpayController::class, 'verifyPayment'])->name('razorpay.verify');
});

// Strictly Student Only Routes
Route::middleware(['auth', 'role:Student'])->prefix('student')->name('student.')->group(function () {

    // Profile & KYC
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/kyc/submit', [ProfileController::class, 'submitKyc'])->name('kyc.submit');
    Route::post('/bank/save', [ProfileController::class, 'saveBank'])->name('bank.save');
    Route::post('/update/password', [ProfileController::class, 'changePassword'])->name('password.change');

    // Learning / LMS
    Route::get('/my-courses', [StudentController::class, 'myCourses'])->name('my-courses');
    Route::get('/beginner-guide', [StudentController::class, 'beginnerGuide'])->name('beginner-guide');
    Route::get('/resources', [StudentController::class, 'resources'])->name('resources');
    Route::get('/watch/{course}/{lesson?}', [StudentController::class, 'watch'])->name('watch');
    Route::post('/progress/update', [StudentController::class, 'updateProgress'])->name('progress.update');
    Route::get('/video-key/{lesson}', [StudentController::class, 'getVideoKey'])->name('video.key');

    // Paid Student Features (Protected by Purchase Check)
    Route::middleware(['check.purchase'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard'); // Note: name is student.dashboard due to prefix

        // Affiliate Links (Original Resource)
        Route::resource('affiliate-links', AffiliateLinkController::class);

        // Affiliate Controller Routes (For Dashboard Integration)
        Route::post('/affiliate/generate-link', [\App\Http\Controllers\AffiliateController::class, 'generateLink'])->name('affiliate.link.generate');
        Route::delete('/affiliate/delete-link/{id}', [\App\Http\Controllers\AffiliateController::class, 'deleteLink'])->name('affiliate.link.delete');

        // Wallet & Payouts
        Route::get('/wallet', [\App\Http\Controllers\Student\WalletController::class, 'index'])->name('wallet.index');
        Route::post('/wallet/withdraw', [\App\Http\Controllers\Student\WalletController::class, 'requestWithdrawal'])->name('wallet.withdraw');

        // Coupons
        Route::get('/coupons', [CouponController::class, 'index'])->name('coupons.index');
        Route::get('/coupons/store', [CouponController::class, 'store'])->name('coupons.store');
        Route::post('/coupons/purchase/initiate', [CouponController::class, 'initiatePurchase'])->name('coupons.purchase.initiate');
        Route::post('/coupons/purchase/verify', [CouponController::class, 'verifyPurchase'])->name('coupons.purchase.verify');
        Route::post('/coupons/transfer', [CouponController::class, 'transfer'])->name('coupons.transfer');

        // Affiliate Dashboard & Features
        Route::get('/affiliate/dashboard', [AffiliateController::class, 'index'])->name('affiliate.dashboard');
        Route::get('/affiliate/leads', [AffiliateController::class, 'leads'])->name('affiliate.leads');
        Route::get('/affiliate/commission-structure', [AffiliateController::class, 'commissionStructure'])->name('affiliate.commission_structure');

        // Invoices
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');

        // Browse Courses
        Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/{id}', [CourseController::class, 'show'])->name('courses.show');
    });
});
