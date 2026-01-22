<?php

use App\Http\Controllers\Admin\RoleController; // Admin folder hata diya
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// 1. Root URL par Login page open hoga
Route::get('/', function () {
    return redirect()->route('login');
});

// 2. Auth Middleware Group (Login ke baad wale pages)
Route::middleware(['auth'])->group(function () {

    // Dashboard Route (Zaroori hai kyunki login isi par redirect karega)
    Route::get('/dashboard', function () {
        return view('dashboard'); // Make sure dashboard.blade.php exists
    })->name('dashboard');

    // Role & User Management Resource Routes
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
});

// 3. Auth Routes File Include
require __DIR__ . '/auth.php';
