<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\WebAuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard.index');
})->name('dashboard');

// Auth Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/login', [WebAuthController::class, 'login'])->name('login.post');
Route::post('/register', [WebAuthController::class, 'register'])->name('register.post');
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/forgot-password', [PasswordResetController::class, 'showRequestForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetCode'])->name('password.send-code');
Route::get('/reset-password', [PasswordResetController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');

// Guest Account Routes
Route::post('/guest/create', [GuestController::class, 'createGuestAccount'])->name('guest.create');
Route::get('/guest/dashboard', [GuestController::class, 'guestDashboard'])->name('guest.dashboard');

// SPA Routes
Route::prefix('spa')->group(function () {
    Route::get('/accounts', function () {
        return view('spa.accounts.index');
    })->name('spa.accounts');
    
    Route::get('/roles', function () {
        return view('spa.roles.index');
    })->name('spa.roles');
    
    Route::get('/permissions', function () {
        return view('spa.permissions.index');
    })->name('spa.permissions');
});

// Accounts Routes
Route::resource('accounts', AccountController::class);
