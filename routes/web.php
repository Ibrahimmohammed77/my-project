<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

// ==================== AUTHENTICATION ROUTES ====================
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);

    // Password Reset
    Route::get('forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
    Route::get('reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// ==================== AUTHENTICATED ROUTES ====================
Route::middleware('auth')->group(function () {
    // Dashboard Routes
    require __DIR__.'/dashboard.php';

    // User Routes
    require __DIR__.'/user.php';

    // Admin Routes
    require __DIR__.'/admin.php';

    // Studio Owner Routes
    require __DIR__.'/studio.php';

    // School Owner Routes
    require __DIR__.'/school.php';

    // Customer Routes
    require __DIR__.'/customer.php';
});

// ==================== PUBLIC ROUTES ====================
Route::prefix('pages')->as('pages.')->group(function () {
    Route::view('/about', 'pages.about')->name('about');
    Route::view('/contact', 'pages.contact')->name('contact');
    Route::view('/privacy', 'pages.privacy')->name('privacy');
    Route::view('/terms', 'pages.terms')->name('terms');
    Route::view('/pricing', 'pages.pricing')->name('pricing');
    Route::view('/features', 'pages.features')->name('features');
});

// CSRF Token for SPA
Route::get('/csrf-token', [AuthController::class, 'getCsrfToken'])->name('csrf-token');

// ==================== ERROR PAGES ====================
Route::fallback(function () {
    return view('errors.404');
});
