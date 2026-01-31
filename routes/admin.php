<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Admin\UserController;
use App\Http\Controllers\Web\Admin\StudioController;
use App\Http\Controllers\Web\Admin\SchoolController;
use App\Http\Controllers\Web\Admin\PlanController;
use App\Http\Controllers\Web\Admin\LookupController;
use App\Http\Controllers\Web\Admin\CardController;
use App\Http\Controllers\Web\Admin\RoleController;
use App\Http\Controllers\Web\Admin\SubscriptionController;
use App\Http\Controllers\Web\Admin\PermissionController;
use App\Http\Controllers\Web\Admin\SubscriberController;

// ==================== ADMIN MANAGEMENT ROUTES ====================

// Users Management
Route::middleware('can:manage_users')->group(function () {
    Route::post('admin/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('admin/accounts', [UserController::class, 'index'])->name('spa.accounts');
    Route::get('admin/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::get('admin/users', function () {
        return redirect()->route('spa.accounts');
    })->name('admin.users.index');

    // Unified Users API
    Route::get('/accounts', [UserController::class, 'index'])->name('accounts.index');
    Route::post('/accounts', [UserController::class, 'store'])->name('accounts.store');
    Route::put('/accounts/{user}', [UserController::class, 'update'])->name('accounts.update');
    Route::delete('/accounts/{user}', [UserController::class, 'destroy'])->name('accounts.destroy');

    // Subscribers
    Route::get('admin/subscribers', [SubscriberController::class, 'index'])->name('spa.subscribers');
});

// User search - accessible by multiple managers
Route::middleware('auth')->get('admin/users/search', [UserController::class, 'search'])->name('admin.users.search');

// Studios Management
Route::middleware('can:manage_studios')->group(function () {
    Route::get('admin/studios', [StudioController::class, 'index'])->name('spa.studios');
    Route::post('admin/studios', [StudioController::class, 'store'])->name('admin.studios.store');
    Route::put('admin/studios/{studio}', [StudioController::class, 'update'])->name('admin.studios.update');
    Route::delete('admin/studios/{studio}', [StudioController::class, 'destroy'])->name('admin.studios.destroy');
    Route::get('/studios', [StudioController::class, 'index']);
});

// Schools Management
Route::middleware('can:manage_schools')->group(function () {
    Route::get('admin/schools', [SchoolController::class, 'index'])->name('spa.schools');
    Route::post('admin/schools', [SchoolController::class, 'store'])->name('admin.schools.store');
    Route::put('admin/schools/{school}', [SchoolController::class, 'update'])->name('admin.schools.update');
    Route::delete('admin/schools/{school}', [SchoolController::class, 'destroy'])->name('admin.schools.destroy');
    Route::get('/schools', [SchoolController::class, 'index']);
});

// Plans Management
Route::middleware('can:manage_plans')->group(function () {
    Route::get('admin/plans', [PlanController::class, 'index'])->name('spa.plans');
    Route::post('admin/plans', [PlanController::class, 'store'])->name('admin.plans.store');
    Route::get('admin/plans/{plan}/edit', [PlanController::class, 'edit'])->name('admin.plans.edit');
    Route::put('admin/plans/{plan}', [PlanController::class, 'update'])->name('admin.plans.update');
    Route::delete('admin/plans/{plan}', [PlanController::class, 'destroy'])->name('admin.plans.destroy');
});

// Lookups Management
Route::middleware('can:manage_lookups')->group(function () {
    Route::get('admin/lookups', [LookupController::class, 'index'])->name('spa.lookups');
    Route::post('admin/lookups/values', [LookupController::class, 'storeValue'])->name('admin.lookups.values.store');
    Route::put('admin/lookups/values/{value}', [LookupController::class, 'updateValue'])->name('admin.lookups.values.update');
    Route::delete('admin/lookups/values/{value}', [LookupController::class, 'destroyValue'])->name('admin.lookups.values.destroy');
    Route::get('/lookups', [LookupController::class, 'index']);
});

// Cards Management
Route::middleware('can:manage_cards')->group(function () {
    // Card Groups
    Route::get('admin/cards', [CardController::class, 'indexGroup'])->name('spa.cards');
    Route::post('admin/cards/groups', [CardController::class, 'storeGroup'])->name('admin.cards.groups.store');
    Route::put('admin/cards/groups/{group}', [CardController::class, 'updateGroup'])->name('admin.cards.groups.update');
    Route::delete('admin/cards/groups/{group}', [CardController::class, 'destroyGroup'])->name('admin.cards.groups.destroy');

    // Cards (Nested)
    Route::get('admin/cards/groups/{group}/cards', [CardController::class, 'indexCards'])->name('admin.cards.groups.cards');
    Route::post('admin/cards/groups/{group}/cards', [CardController::class, 'storeCard'])->name('admin.cards.groups.cards.store');
    Route::put('admin/cards/groups/{group}/cards/{card}', [CardController::class, 'updateCard'])->name('admin.cards.groups.cards.update');
    Route::delete('admin/cards/groups/{group}/cards/{card}', [CardController::class, 'destroyCard'])->name('admin.cards.groups.cards.destroy');

    // Unified Cards API
    Route::get('/cards', [CardController::class, 'indexGroup'])->name('cards.index');
});

// Roles Management
Route::middleware('can:manage_roles')->group(function () {
    Route::get('admin/roles', [RoleController::class, 'index'])->name('spa.roles');
    Route::post('admin/roles', [RoleController::class, 'store'])->name('admin.roles.store');
    Route::put('admin/roles/{role}', [RoleController::class, 'update'])->name('admin.roles.update');
    Route::delete('admin/roles/{role}', [RoleController::class, 'destroy'])->name('admin.roles.destroy');
    Route::get('/roles', [RoleController::class, 'index']);
});

// Subscriptions Management
Route::middleware('can:manage_subscriptions')->group(function () {
    Route::get('admin/subscriptions', [SubscriptionController::class, 'index'])->name('spa.subscriptions');
    Route::post('admin/subscriptions', [SubscriptionController::class, 'store'])->name('admin.subscriptions.store');
    Route::put('admin/subscriptions/{subscription}', [SubscriptionController::class, 'update'])->name('admin.subscriptions.update');
    Route::delete('admin/subscriptions/{subscription}', [SubscriptionController::class, 'destroy'])->name('admin.subscriptions.destroy');
});

// Permissions Management
Route::middleware('can:manage_permissions')->group(function () {
    Route::get('admin/permissions', [PermissionController::class, 'index'])->name('spa.permissions');
    Route::get('/permissions', [PermissionController::class, 'index']);
});
