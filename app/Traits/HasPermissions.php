<?php

namespace App\Traits;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasPermissions
{
    /**
     * Get user roles.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id')
                    ->withPivot('is_active')
                    ->withTimestamps()
                    ->wherePivot('is_active', true);
    }

    /**
     * Get user permissions with caching.
     */
    public function permissions()
    {
        return cache()->remember("user_{$this->id}_permissions", 300, function () {
            return $this->roles()
                ->where('roles.is_active', true)
                ->with('permissions')
                ->get()
                ->pluck('permissions')
                ->flatten()
                ->unique('permission_id');
        });
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole($role): bool
    {
        $cacheKey = "user_{$this->id}_has_role_" . (is_string($role) ? $role : $role->role_id);
        
        return cache()->remember($cacheKey, 300, function () use ($role) {
            if (is_string($role)) {
                return $this->roles()->where('name', $role)->exists();
            }

            if ($role instanceof Role) {
                return $this->roles()->where('roles.role_id', $role->role_id)->exists();
            }

            return false;
        });
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission($permission): bool
    {
        $cacheKey = "user_{$this->id}_has_permission_" . (is_string($permission) ? $permission : $permission->permission_id);
        
        return cache()->remember($cacheKey, 300, function () use ($permission) {
            return $this->permissions()->contains(function ($perm) use ($permission) {
                if (is_string($permission)) {
                    return $perm->name === $permission;
                }

                if ($permission instanceof Permission) {
                    return $perm->permission_id === $permission->permission_id;
                }

                return false;
            });
        });
    }

    /**
     * Assign a role to the user.
     */
    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }

        if (!$this->hasRole($role)) {
            $this->roles()->attach($role->role_id, ['is_active' => true]);
            $this->forgetCachedPermissions();
        }

        return $this;
    }

    /**
     * Forget cached permissions.
     */
    public function forgetCachedPermissions()
    {
        cache()->forget("user_{$this->id}_permissions");
    }

    /**
     * Is admin attribute.
     */
    public function getIsAdminAttribute(): bool
    {
        return $this->hasRole('admin') || $this->hasRole('super_admin');
    }
}
