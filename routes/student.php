<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\DashboardController;

Route::middleware(['auth', 'role:Student'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

});
