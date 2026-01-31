<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;

// User Profile & Authentication
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('profile', [AuthController::class, 'profile'])->name('profile');
Route::put('profile', [AuthController::class, 'updateProfile']);

// Email Verification
Route::get('verify-email', [AuthController::class, 'showVerifyEmailForm'])->name('verification.notice');
Route::post('verify-email', [AuthController::class, 'verifyEmail'])->name('verification.verify');
Route::post('resend-verification', [AuthController::class, 'resendVerification'])->name('verification.send');

// Password Management
Route::get('change-password', [AuthController::class, 'showChangePasswordForm'])->name('password.change');
Route::post('change-password', [AuthController::class, 'changePassword']);

// Profile Completion
Route::get('profile/completion', function () {
    return view('auth.profile-completion');
})->name('profile.completion');
