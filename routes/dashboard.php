<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\DashboardController;

// Dashboard Redirection
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

Route::get('/dashboard/final-user', [DashboardController::class, 'finalUser'])
    ->name('dashboard.final_user')
    ->middleware('can:access-final-user-dashboard');

Route::get('/dashboard/editor', [DashboardController::class, 'editor'])
    ->name('dashboard.editor')
    ->middleware('can:access-editor-dashboard');

Route::get('/dashboard/guest', [DashboardController::class, 'guest'])
    ->name('dashboard.guest')
    ->middleware('can:access-guest-dashboard');

// AJAX endpoints for dashboard statistics
Route::middleware(['auth', 'can:access-admin-dashboard'])->group(function () {
    Route::get('/dashboard/admin/stats', [DashboardController::class, 'getAdminStatsJson'])
        ->name('dashboard.admin.stats');

    Route::get('/dashboard/admin/activity', [DashboardController::class, 'getRecentActivityJson'])
        ->name('dashboard.admin.activity');
});

Route::middleware(['auth', 'can:access-studio-dashboard'])->group(function () {
    Route::get('/dashboard/studio/stats', [DashboardController::class, 'getStudioStatsJson'])
        ->name('dashboard.studio.stats');
});

Route::middleware(['auth', 'can:access-school-dashboard'])->group(function () {
    Route::get('/dashboard/school/stats', [DashboardController::class, 'getSchoolStatsJson'])
        ->name('dashboard.school.stats');
});
