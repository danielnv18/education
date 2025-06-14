<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\SendPasswordResetLinkController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class);
    Route::post('users/{user}/send-password-reset-link', SendPasswordResetLinkController::class)->name('users.send-password-reset-link');
});
