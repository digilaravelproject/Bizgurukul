<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/login', function () {
    return redirect()->route('login');
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/course/{course}', [HomeController::class, 'courses'])
    ->name('course.show');
Route::get('/coursesp/{course}', [HomeController::class, 'courses'])
    ->name('bundles.show');
Route::post('/check-referral', [RegisteredUserController::class, 'checkReferral'])->name('check.referral');

// Common Profile Routes (Sabke liye same)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::middleware(['auth', 'role:Student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/profile', [App\Http\Controllers\Student\ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [App\Http\Controllers\Student\ProfileController::class, 'updateProfile'])->name('profile.update'); // Custom Update
    Route::post('/kyc/submit', [App\Http\Controllers\Student\ProfileController::class, 'submitKyc'])->name('kyc.submit');
    Route::post('/bank/save', [App\Http\Controllers\Student\ProfileController::class, 'saveBank'])->name('bank.save');
});

// Auth Routes (Login/Register)
require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/student.php';
