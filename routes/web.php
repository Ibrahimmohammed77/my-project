<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\WebAuthController;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\DashboardController;

// Public Auth Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/login', [WebAuthController::class, 'login'])->name('login.post');
Route::post('/register', [WebAuthController::class, 'register'])->name('register.post');

// Password Reset Routes
Route::get('/forgot-password', [PasswordResetController::class, 'showRequestForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetCode'])->name('password.send-code');
Route::get('/reset-password', [PasswordResetController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');

// Guest Account Routes (Assuming these are public, or do they need specific guest auth? Leaving public for now as they create guests)
Route::post('/guest/create', [GuestController::class, 'createGuestAccount'])->name('guest.create');
Route::get('/guest/dashboard', [GuestController::class, 'guestDashboard'])->name('guest.dashboard');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

    // Roles & Permissions standard resources
    Route::resource('roles', \App\Http\Controllers\RoleController::class);
    Route::resource('permissions', \App\Http\Controllers\PermissionController::class);

    // SPA route aliases (to support existing sidebar links)
    Route::get('/spa/accounts', [AccountController::class, 'index'])->name('spa.accounts');
    Route::get('/spa/roles', [\App\Http\Controllers\RoleController::class, 'index'])->name('spa.roles');
    Route::get('/spa/permissions', [\App\Http\Controllers\PermissionController::class, 'index'])->name('spa.permissions');
    
    // New SPA Alias for Studios and Schools
    Route::get('/spa/studios', [\App\Http\Controllers\StudioController::class, 'index'])->name('spa.studios');
    Route::get('/spa/schools', [\App\Http\Controllers\SchoolController::class, 'index'])->name('spa.schools');

    // Accounts Routes
    Route::resource('accounts', AccountController::class);
    Route::resource('studios', \App\Http\Controllers\StudioController::class);
    Route::resource('schools', \App\Http\Controllers\SchoolController::class);
    // SPA Alias for Customers
    Route::get('/spa/customers', [\App\Http\Controllers\CustomerController::class, 'index'])->name('spa.customers');

    // Customer Resource
    Route::resource('customers', \App\Http\Controllers\CustomerController::class);

    // SPA Alias for Subscribers
    Route::get('/spa/subscribers', [\App\Http\Controllers\SubscriberController::class, 'index'])->name('spa.subscribers');

    // Subscriber Resource
    Route::resource('subscribers', \App\Http\Controllers\SubscriberController::class);
});
