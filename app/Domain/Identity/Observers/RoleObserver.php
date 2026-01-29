<?php

namespace App\Domain\Identity\Observers;

use App\Domain\Identity\Models\Role;
use App\Domain\Shared\Models\ActivityLog;

class RoleObserver
{
    /**
     * Handle the Role "created" event.
     */
    public function created(Role $role): void
    {
        ActivityLog::create([
            'account_id' => auth()->id() ?? null,
            'action' => 'ROLE_CREATED',
            'resource_type' => 'roles',
            'resource_id' => $role->role_id,
            'metadata' => [
                'name' => $role->name,
                'is_system' => $role->is_system,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle the Role "updated" event.
     */
    public function updated(Role $role): void
    {
        ActivityLog::create([
            'account_id' => auth()->id() ?? null,
            'action' => 'ROLE_UPDATED',
            'resource_type' => 'roles',
            'resource_id' => $role->role_id,
            'metadata' => [
                'name' => $role->name,
                'changes' => $role->getChanges(),
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle the Role "deleted" event.
     */
    public function deleted(Role $role): void
    {
        // Prevent deletion of system roles (double check)
        if ($role->is_system) {
            return;
        }

        ActivityLog::create([
            'account_id' => auth()->id() ?? null,
            'action' => 'ROLE_DELETED',
            'resource_type' => 'roles',
            'resource_id' => $role->role_id,
            'metadata' => [
                'name' => $role->name,
                'is_system' => $role->is_system,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
