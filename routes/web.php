<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardRedirectController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (Global & Public)
|--------------------------------------------------------------------------
*/

// 1. Affiliate Redirect (Keep at top to catch slugs early)
Route::get('/u/{slug}', [App\Http\Controllers\Student\AffiliateLinkController::class, 'handleRedirect'])
    ->name('affiliate.redirect');

// 2. Public Pages
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home-variant', [HomeController::class, 'variant'])->name('web.variant');
Route::get('/about', [HomeController::class, 'about'])->name('web.about');
Route::get('/contact', [HomeController::class, 'contact'])->name('web.contact');
Route::get('/course/{course}', [HomeController::class, 'courses'])->name('course.show');
Route::get('/coursesp/{course}', [HomeController::class, 'courses'])->name('bundles.show');

// 3. Guest/Registration Payment Flow (Publicly Accessible)
// These handle the initial payment during registration
Route::post('payment/initiate', [\App\Http\Controllers\Auth\RegistrationFlowController::class, 'initiatePayment'])
    ->name('payment.initiate');

Route::post('payment/verify', [\App\Http\Controllers\Auth\RegistrationFlowController::class, 'verifyPayment'])
    ->name('payment.verify'); // URI: domain.com/payment/verify

// 4. Common Profile Routes (For all Auth users: Admin & Student)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard Redirect
    Route::get('/dashboard', [DashboardRedirectController::class, 'index'])->name('dashboard');

    // Onboarding (Common)
    Route::get('/onboarding/referral', [App\Http\Controllers\OnboardingController::class, 'showReferralStep'])->name('onboarding.referral');
    Route::post('/onboarding/referral', [App\Http\Controllers\OnboardingController::class, 'storeReferrer'])->name('onboarding.referral.store');
    Route::get('/onboarding/skip', [App\Http\Controllers\OnboardingController::class, 'skip'])->name('onboarding.skip');
});

// 5. Load Separate Route Files
require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/student.php';
