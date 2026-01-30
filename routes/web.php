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

    Route::get('/dashboard/final-user', [DashboardController::class, 'finalUser'])
        ->name('dashboard.final_user')
        ->middleware('can:access-final-user-dashboard');

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
    Route::middleware('can:is-studio-owner')->as('studio.')->prefix('studio')->group(function () {
        Route::resource('albums', \App\Http\Controllers\Studio\AlbumController::class);
        Route::resource('customers', \App\Http\Controllers\Studio\CustomerController::class);
        Route::resource('cards', \App\Http\Controllers\Studio\CardController::class)->parameters(['cards' => 'card:card_id']);
        Route::post('cards/{card:card_id}/link-albums', [\App\Http\Controllers\Studio\CardController::class, 'linkAlbums'])->name('cards.link-albums');
        
        // Studio Profile Update
        Route::get('profile', [\App\Http\Controllers\Studio\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [\App\Http\Controllers\Studio\ProfileController::class, 'update'])->name('profile.update');
        
        // Storage Allocation
        Route::get('storage/libraries', [\App\Http\Controllers\Studio\StorageLibraryController::class, 'index'])->name('storage.index');
        Route::post('storage/libraries', [\App\Http\Controllers\Studio\StorageLibraryController::class, 'store'])->name('storage.store');
        Route::put('storage/libraries/{library}', [\App\Http\Controllers\Studio\StorageLibraryController::class, 'update'])->name('storage.update');
        Route::delete('storage/libraries/{library}', [\App\Http\Controllers\Studio\StorageLibraryController::class, 'destroy'])->name('storage.destroy');
        
        // Photo Review
        Route::get('photo-review/pending', [\App\Http\Controllers\Studio\PhotoReviewController::class, 'pending'])->name('photo-review.pending');
        Route::post('photo-review/{photo}/review', [\App\Http\Controllers\Studio\PhotoReviewController::class, 'review'])->name('photo-review.review');
    });

    Route::middleware('can:is-school-owner')->as('school.')->prefix('school')->group(function () {
        Route::resource('albums', \App\Http\Controllers\School\AlbumController::class);
        Route::resource('cards', \App\Http\Controllers\School\CardController::class)->parameters(['cards' => 'card:card_id']);
        Route::post('cards/{card:card_id}/link-albums', [\App\Http\Controllers\School\CardController::class, 'linkAlbums'])->name('cards.link-albums');
        Route::get('students', [\App\Http\Controllers\School\StudentController::class, 'index'])->name('students.index');
        Route::get('students/{student}', [\App\Http\Controllers\School\StudentController::class, 'show'])->name('students.show');
        
        // School Profile Update
        Route::get('profile', [\App\Http\Controllers\School\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [\App\Http\Controllers\School\ProfileController::class, 'update'])->name('profile.update');
    });

    Route::middleware('can:is-customer')->group(function () {
        // Route::resource('my-albums', \App\Http\Controllers\Customer\AlbumController::class);
        // Route::resource('my-cards', \App\Http\Controllers\Customer\CardController::class);
        
        // Subscriber uploads
        Route::post('customer/photos/upload', [\App\Http\Controllers\Customer\PhotoController::class, 'store'])->name('customer.photos.store');
    });

    Route::middleware('can:manage_users')->group(function () {
        Route::post('admin/users', [\App\Http\Controllers\Web\Admin\UserController::class, 'store'])->name('admin.users.store');
        Route::get('admin/accounts', [\App\Http\Controllers\Web\Admin\UserController::class, 'index'])->name('spa.accounts');
        Route::get('admin/users/{user}/edit', [\App\Http\Controllers\Web\Admin\UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('admin/users/{user}', [\App\Http\Controllers\Web\Admin\UserController::class, 'update'])->name('admin.users.update');
        Route::get('admin/users', function () {
            return redirect()->route('spa.accounts');
        })->name('admin.users.index');
    });

    Route::middleware('can:manage_plans')->group(function () {
        Route::get('admin/plans', [\App\Http\Controllers\Web\Admin\PlanController::class, 'index'])->name('spa.plans');
        Route::post('admin/plans', [\App\Http\Controllers\Web\Admin\PlanController::class, 'store'])->name('admin.plans.store');
        Route::get('admin/plans/{plan}/edit', [\App\Http\Controllers\Web\Admin\PlanController::class, 'edit'])->name('admin.plans.edit');
        Route::put('admin/plans/{plan}', [\App\Http\Controllers\Web\Admin\PlanController::class, 'update'])->name('admin.plans.update');
        Route::delete('admin/plans/{plan}', [\App\Http\Controllers\Web\Admin\PlanController::class, 'destroy'])->name('admin.plans.destroy');
    });

    Route::middleware('can:manage_lookups')->group(function () {
        Route::get('admin/lookups', [\App\Http\Controllers\Web\Admin\LookupController::class, 'index'])->name('spa.lookups');
        Route::post('admin/lookups/values', [\App\Http\Controllers\Web\Admin\LookupController::class, 'storeValue'])->name('admin.lookups.values.store');
        Route::put('admin/lookups/values/{value}', [\App\Http\Controllers\Web\Admin\LookupController::class, 'updateValue'])->name('admin.lookups.values.update');
        Route::delete('admin/lookups/values/{value}', [\App\Http\Controllers\Web\Admin\LookupController::class, 'destroyValue'])->name('admin.lookups.values.destroy');
    });

    Route::middleware('can:manage_cards')->group(function () {
        // Card Groups
        Route::get('admin/cards', [\App\Http\Controllers\Web\Admin\CardController::class, 'indexGroup'])->name('spa.cards');
        Route::post('admin/cards/groups', [\App\Http\Controllers\Web\Admin\CardController::class, 'storeGroup'])->name('admin.cards.groups.store');
        Route::put('admin/cards/groups/{group}', [\App\Http\Controllers\Web\Admin\CardController::class, 'updateGroup'])->name('admin.cards.groups.update');
        Route::delete('admin/cards/groups/{group}', [\App\Http\Controllers\Web\Admin\CardController::class, 'destroyGroup'])->name('admin.cards.groups.destroy');

        // Cards (Nested)
        Route::get('admin/cards/groups/{group}/cards', [\App\Http\Controllers\Web\Admin\CardController::class, 'indexCards'])->name('admin.cards.groups.cards');
        Route::post('admin/cards/groups/{group}/cards', [\App\Http\Controllers\Web\Admin\CardController::class, 'storeCard'])->name('admin.cards.groups.cards.store');
        Route::put('admin/cards/groups/{group}/cards/{card}', [\App\Http\Controllers\Web\Admin\CardController::class, 'updateCard'])->name('admin.cards.groups.cards.update');
        Route::delete('admin/cards/groups/{group}/cards/{card}', [\App\Http\Controllers\Web\Admin\CardController::class, 'destroyCard'])->name('admin.cards.groups.cards.destroy');
    });

    Route::middleware('can:manage_roles')->group(function () {
        Route::get('admin/roles', [\App\Http\Controllers\Web\Admin\RoleController::class, 'index'])->name('spa.roles');
        Route::post('admin/roles', [\App\Http\Controllers\Web\Admin\RoleController::class, 'store'])->name('admin.roles.store');
        Route::put('admin/roles/{role}', [\App\Http\Controllers\Web\Admin\RoleController::class, 'update'])->name('admin.roles.update');
        Route::delete('admin/roles/{role}', [\App\Http\Controllers\Web\Admin\RoleController::class, 'destroy'])->name('admin.roles.destroy');
        Route::get('/roles', [\App\Http\Controllers\Web\Admin\RoleController::class, 'index']);
    });

    Route::middleware('can:manage_subscriptions')->group(function () {
        Route::get('admin/subscriptions', [\App\Http\Controllers\Web\Admin\SubscriptionController::class, 'index'])->name('spa.subscriptions');
        Route::post('admin/subscriptions', [\App\Http\Controllers\Web\Admin\SubscriptionController::class, 'store'])->name('admin.subscriptions.store');
        Route::delete('admin/subscriptions/{subscription}', [\App\Http\Controllers\Web\Admin\SubscriptionController::class, 'destroy'])->name('admin.subscriptions.destroy');
    });

    Route::middleware('can:manage_studios')->group(function () {
        Route::get('admin/studios', [\App\Http\Controllers\Web\Admin\StudioController::class, 'index'])->name('spa.studios');
        Route::get('/studios', [\App\Http\Controllers\Web\Admin\StudioController::class, 'index']);
    });

    Route::middleware('can:manage_schools')->group(function () {
        Route::get('admin/schools', [\App\Http\Controllers\Web\Admin\SchoolController::class, 'index'])->name('spa.schools');
        Route::get('/schools', [\App\Http\Controllers\Web\Admin\SchoolController::class, 'index']);
    });

    Route::middleware('can:manage_permissions')->group(function () {
        Route::get('admin/permissions', [\App\Http\Controllers\Web\Admin\PermissionController::class, 'index'])->name('spa.permissions');
        Route::get('/permissions', [\App\Http\Controllers\Web\Admin\PermissionController::class, 'index']);
    });

    // Root-level routes for other SPA services
    Route::get('/accounts', [\App\Http\Controllers\Web\Admin\UserController::class, 'index'])->middleware('can:manage_users')->name('accounts.index');
    Route::post('/accounts', [\App\Http\Controllers\Web\Admin\UserController::class, 'store'])->middleware('can:manage_users')->name('accounts.store');
    Route::put('/accounts/{user}', [\App\Http\Controllers\Web\Admin\UserController::class, 'update'])->middleware('can:manage_users')->name('accounts.update');
    Route::delete('/accounts/{user}', [\App\Http\Controllers\Web\Admin\UserController::class, 'destroy'])->middleware('can:manage_users')->name('accounts.destroy');
    Route::get('/lookups', [\App\Http\Controllers\Web\Admin\LookupController::class, 'index'])->middleware('can:manage_lookups');
    Route::get('/plans', [\App\Http\Controllers\Web\Admin\PlanController::class, 'index'])->middleware('can:manage_plans');
    Route::get('/cards', [\App\Http\Controllers\Web\Admin\CardController::class, 'indexGroup'])->middleware('can:manage_cards');

    Route::middleware('can:is-admin')->group(function () {
        // Route::resource('admin/studios', \App\Http\Controllers\Admin\StudioController::class);
        // Route::resource('admin/schools', \App\Http\Controllers\Admin\SchoolController::class);
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
