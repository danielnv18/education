<?php

declare(strict_types=1);

use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseStudentsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnrollStudentController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::resource('courses', CourseController::class);
    Route::post('courses/{course}/enroll', EnrollStudentController::class)->name('courses.enroll');
    Route::get('courses/{course}/students', CourseStudentsController::class)->name('courses.students');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
