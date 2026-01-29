<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\CourseController;

Route::middleware(['auth', 'role:Student'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
// Courses List Route
Route::get('/courses', [CourseController::class, 'index'])->name('student.courses.index');
// Course Details Route
Route::get('/courses/{id}', [CourseController::class, 'show'])->name('student.courses.show');
