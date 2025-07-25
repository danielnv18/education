<?php

declare(strict_types=1);

use App\Http\Controllers\Course\CourseContentController;
use App\Http\Controllers\Course\CourseController;
use App\Http\Controllers\Course\CourseStudentsController;
use App\Http\Controllers\Course\LessonController;
use App\Http\Controllers\Course\ModuleController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::resource('courses', CourseController::class);
    Route::resource('courses.students', CourseStudentsController::class)->only(['index', 'store']);

    Route::resource('courses.content', CourseContentController::class)->only(['index', 'store']);

    Route::resource('courses.modules', ModuleController::class)->only(['store', 'update', 'destroy']);
    Route::resource('courses.lessons', LessonController::class)->only(['store', 'update', 'destroy']);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
