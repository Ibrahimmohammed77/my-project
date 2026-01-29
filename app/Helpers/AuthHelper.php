<?php

use App\Models\User;
use App\Services\Contracts\AuthServiceInterface;
use Illuminate\Support\Facades\Auth;

if (!function_exists('current_user')) {
    /**
     * Get current authenticated user with relations.
     */
    function current_user(): ?User
    {
        $user = Auth::user();

        if ($user && !$user->relationLoaded('roles')) {
            $user->load([
                'status:id,code,name',
                'type:id,code,name',
                'roles:id,name',
                'customer',
                'storageAccount',
                'activeSubscription',
            ]);
        }

        return $user;
    }
}

if (!function_exists('is_admin')) {
    /**
     * Check if current user is admin.
     */
    function is_admin(): bool
    {
        $user = current_user();
        return $user && $user->hasRole('admin');
    }
}

if (!function_exists('is_studio_owner')) {
    /**
     * Check if current user is studio owner.
     */
    function is_studio_owner(): bool
    {
        $user = current_user();
        return $user && ($user->hasRole('studio_owner') || $user->studio()->exists());
    }
}

if (!function_exists('is_customer')) {
    /**
     * Check if current user is customer.
     */
    function is_customer(): bool
    {
        $user = current_user();
        return $user && ($user->hasRole('customer') || $user->customer()->exists());
    }
}

if (!function_exists('has_permission')) {
    /**
     * Check if current user has permission.
     */
    function has_permission(string $permission): bool
    {
        $user = current_user();
        return $user && $user->hasPermission($permission);
    }
}

if (!function_exists('has_active_subscription')) {
    /**
     * Check if current user has active subscription.
     */
    function has_active_subscription(): bool
    {
        $user = current_user();
        return $user && $user->activeSubscription()->exists();
    }
}

if (!function_exists('auth_service')) {
    /**
     * Get auth service instance.
     */
    function auth_service(): AuthServiceInterface
    {
        return app(AuthServiceInterface::class);
    }
}

if (!function_exists('login_user')) {
    /**
     * Login user programmatically.
     */
    function login_user($login, $password, $remember = false): array
    {
        try {
            return auth_service()->login($login, $password, $remember);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

if (!function_exists('logout_user')) {
    /**
     * Logout current user.
     */
    function logout_user(): void
    {
        auth_service()->logout();
    }
}

if (!function_exists('get_user_by_login')) {
    /**
     * Get user by email, phone, or username.
     */
    function get_user_by_login(string $login): ?User
    {
        $provider = app(\App\Providers\CustomUserProvider::class);
        return $provider->retrieveByLogin($login);
    }
}

if (!function_exists('create_user_token')) {
    /**
     * Create API token for user.
     */
    function create_user_token(User $user, string $name = 'auth_token', array $abilities = ['*']): string
    {
        return $user->createToken($name, $abilities)->plainTextToken;
    }
}

if (!function_exists('validate_password')) {
    /**
     * Validate password against user.
     */
    function validate_password(User $user, string $password): bool
    {
        return \Illuminate\Support\Facades\Hash::check($password, $user->password);
    }
}
