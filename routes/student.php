<?php

use Illuminate\Support\Facades\Route;

// Student Routes (Prefix: /student ya normal /dashboard)
Route::middleware(['auth', 'role:Student'])->group(function () {

    // Student Dashboard (Iska naam 'dashboard' hi rakhenge taaki redirect logic kaam kare)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

});
