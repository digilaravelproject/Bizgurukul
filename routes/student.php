<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\CourseController;

use App\Http\Controllers\Student\RazorpayController;

Route::middleware(['auth', 'role:Student|Admin'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

use App\Http\Controllers\Student\StudentController;

Route::middleware(['auth'])->prefix('student')->name('student.')->group(function () {
    Route::get('/my-courses', [StudentController::class, 'myCourses'])->name('my-courses');
    Route::get('/watch/{course}/{lesson?}', [StudentController::class, 'watch'])->name('watch');
    Route::post('/progress/update', [StudentController::class, 'updateProgress'])->name('progress.update');
});
// Courses List Route
Route::get('/courses', [CourseController::class, 'index'])->name('student.courses.index');
// Course Details Route
Route::get('/courses/{id}', [CourseController::class, 'show'])->name('student.courses.show');

Route::post('/payment/create/{courseId}', [RazorpayController::class, 'createOrder'])->name('razorpay.create');
Route::post('/payment/verify', [RazorpayController::class, 'verifyPayment'])->name('razorpay.verify');
