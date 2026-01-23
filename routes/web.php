<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Note: Dashboard route yahan se hata diya hai kyunki wo ab student.php aur admin.php mein hai

// Common Profile Routes (Sabke liye same)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Auth Routes (Login/Register)
require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/student.php';
