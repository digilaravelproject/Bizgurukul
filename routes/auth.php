<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\RegistrationFlowController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Standard Register/Login
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Helper Checks
    Route::post('check-email', [AuthenticatedSessionController::class, 'checkEmail'])->name('login.check-email');
    Route::post('/check-referral', [RegisteredUserController::class, 'checkReferral'])->name('check.referral');

    // Registration Flow (Steps)
    Route::get('register/step-1', [RegistrationFlowController::class, 'showPhase1'])->name('register.phase1');
    Route::post('register/step-1', [RegistrationFlowController::class, 'storePhase1'])->name('register.phase1.store');

    Route::get('register/step-2', [RegistrationFlowController::class, 'showPhase2'])->name('register.phase2');
    Route::post('register/step-2', [RegistrationFlowController::class, 'storePhase2'])->name('register.phase2.store');
    Route::post('register/check-referral', [RegistrationFlowController::class, 'checkReferralPhase2'])->name('register.check-referral');

    Route::get('register/step-3', [RegistrationFlowController::class, 'showPhase3'])->name('register.phase3');
    Route::post('register/check-coupon', [RegistrationFlowController::class, 'checkCoupon'])->name('register.check-coupon');

    // Password Reset
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware('throttle:6,1')->name('verification.send');
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
