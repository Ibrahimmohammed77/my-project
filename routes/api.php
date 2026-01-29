<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\API\AuthController;
// use App\Http\Controllers\API\AccountController;
// use App\Http\Controllers\API\RoleController;
// use App\Http\Controllers\API\PermissionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// // Public routes (لا تحتاج مصادقة)
// Route::prefix('auth')->group(function () {
//     Route::post('/login', [AuthController::class, 'login']);
//     Route::post('/register', [AuthController::class, 'register']);
// });

// // Protected routes (تحتاج مصادقة - using web session for SPA)
// Route::middleware('auth:web')->group(function () {
    
//     // Auth routes
//     Route::prefix('auth')->group(function () {
//         Route::post('/logout', [AuthController::class, 'logout']);
//         Route::get('/me', [AuthController::class, 'me']);
//         Route::put('/profile', [AuthController::class, 'updateProfile']);
//         Route::put('/change-password', [AuthController::class, 'changePassword']);
//     });

//     // Accounts routes
//     Route::prefix('accounts')->group(function () {
//         Route::get('/', [AccountController::class, 'index']);
//         Route::post('/', [AccountController::class, 'store']);
//         Route::get('/{id}', [AccountController::class, 'show']);
//         Route::put('/{id}', [AccountController::class, 'update']);
//         Route::delete('/{id}', [AccountController::class, 'destroy']);
        
//         // Roles management
//         Route::get('/{id}/roles', [AccountController::class, 'getRoles']);
//         Route::post('/{id}/roles', [AccountController::class, 'assignRole']);
//         Route::delete('/{id}/roles/{roleId}', [AccountController::class, 'removeRole']);
        
//         // Permissions
//         Route::get('/{id}/permissions', [AccountController::class, 'getPermissions']);
//     });

//     // Roles routes
//     Route::prefix('roles')->group(function () {
//         Route::get('/', [RoleController::class, 'index']);
//         Route::post('/', [RoleController::class, 'store']);
//         Route::get('/{id}', [RoleController::class, 'show']);
//         Route::put('/{id}', [RoleController::class, 'update']);
//         Route::delete('/{id}', [RoleController::class, 'destroy']);
        
//         // Permissions management
//         Route::get('/{id}/permissions', [RoleController::class, 'getPermissions']);
//         Route::post('/{id}/permissions', [RoleController::class, 'assignPermission']);
//         Route::delete('/{id}/permissions/{permissionId}', [RoleController::class, 'removePermission']);
//         Route::put('/{id}/permissions/sync', [RoleController::class, 'syncPermissions']);
//     });

//     // Permissions routes
//     Route::prefix('permissions')->group(function () {
//         Route::get('/', [PermissionController::class, 'index']);
//         Route::post('/', [PermissionController::class, 'store']);
//         Route::get('/{id}', [PermissionController::class, 'show']);
//         Route::put('/{id}', [PermissionController::class, 'update']);
//         Route::delete('/{id}', [PermissionController::class, 'destroy']);
//     });
// });
