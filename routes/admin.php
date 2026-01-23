<?php

use Illuminate\Support\Facades\Route;

// Admin Routes (Prefix: /admin)
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->group(function () {

    // Admin Dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard'); // Ensure ye view exist kare
    })->name('admin.dashboard');
});
