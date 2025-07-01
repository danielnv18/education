<?php

declare(strict_types=1);

use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseStudentsController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::resource('courses', CourseController::class);
    Route::resource('courses.students', CourseStudentsController::class)->only(['index', 'store']);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
