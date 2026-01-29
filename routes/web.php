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

// Authentication Routes
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

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Dashboard Redirection (Main Entry Point)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/redirect', [DashboardController::class, 'redirect'])->name('dashboard.redirect');

    // Role-based Dashboards
    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])
        ->name('dashboard.admin')
        ->middleware('can:access-admin-dashboard');

    Route::get('/dashboard/studio-owner', [DashboardController::class, 'studioOwner'])
        ->name('dashboard.studio-owner')
        ->middleware('can:access-studio-dashboard');

    Route::get('/dashboard/school-owner', [DashboardController::class, 'schoolOwner'])
        ->name('dashboard.school-owner')
        ->middleware('can:access-school-dashboard');

    Route::get('/dashboard/customer', [DashboardController::class, 'customer'])
        ->name('dashboard.customer')
        ->middleware('can:access-customer-dashboard');

    Route::get('/dashboard/employee', [DashboardController::class, 'employee'])
        ->name('dashboard.employee')
        ->middleware('can:access-employee-dashboard');

    Route::get('/dashboard/editor', [DashboardController::class, 'editor'])
        ->name('dashboard.editor')
        ->middleware('can:access-editor-dashboard');

    Route::get('/dashboard/guest', [DashboardController::class, 'guest'])
        ->name('dashboard.guest')
        ->middleware('can:access-guest-dashboard');

    // Logout
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Profile
    Route::get('profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('profile', [AuthController::class, 'updateProfile']);

    // Email Verification
    Route::get('verify-email', [AuthController::class, 'showVerifyEmailForm'])->name('verification.notice');
    Route::post('verify-email', [AuthController::class, 'verifyEmail'])->name('verification.verify');
    Route::post('resend-verification', [AuthController::class, 'resendVerification'])->name('verification.send');

    // Change Password
    Route::get('change-password', [AuthController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('change-password', [AuthController::class, 'changePassword']);

    // Profile Completion
    Route::get('profile/completion', function () {
        return view('auth.profile-completion');
    })->name('profile.completion');

    // Role-specific Resources
    Route::middleware('can:is-studio-owner')->group(function () {
        Route::resource('studio/albums', \App\Http\Controllers\Studio\AlbumController::class);
        Route::resource('studio/customers', \App\Http\Controllers\Studio\CustomerController::class);
        Route::resource('studio/cards', \App\Http\Controllers\Studio\CardController::class);
    });

    Route::middleware('can:is-customer')->group(function () {
        Route::resource('my-albums', \App\Http\Controllers\Customer\AlbumController::class);
        Route::resource('my-cards', \App\Http\Controllers\Customer\CardController::class);
    });

    Route::middleware('can:is-admin')->group(function () {
        Route::resource('admin/users', \App\Http\Controllers\Admin\UserController::class);
        Route::resource('admin/studios', \App\Http\Controllers\Admin\StudioController::class);
        Route::resource('admin/schools', \App\Http\Controllers\Admin\SchoolController::class);
    });
});

// Static Pages
Route::view('/about', 'pages.about')->name('about');
Route::view('/contact', 'pages.contact')->name('contact');
Route::view('/privacy', 'pages.privacy')->name('privacy');
Route::view('/terms', 'pages.terms')->name('terms');
Route::view('/pricing', 'pages.pricing')->name('pricing');
Route::view('/features', 'pages.features')->name('features');

// 404 Page
Route::fallback(function () {
    return view('errors.404');
});
