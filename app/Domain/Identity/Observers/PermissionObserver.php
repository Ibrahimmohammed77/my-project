<?php

namespace App\Domain\Identity\Observers;

use App\Domain\Identity\Models\Permission;
use App\Domain\Shared\Models\ActivityLog;

class PermissionObserver
{
    /**
     * Handle the Permission "created" event.
     */
    public function created(Permission $permission): void
    {
        ActivityLog::create([
            'account_id' => auth()->id() ?? null,
            'action' => 'PERMISSION_CREATED',
            'resource_type' => 'permissions',
            'resource_id' => $permission->permission_id,
            'metadata' => [
                'name' => $permission->name,
                'resource_type' => $permission->resource_type,
                'action' => $permission->action,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle the Permission "updated" event.
     */
    public function updated(Permission $permission): void
    {
        ActivityLog::create([
            'account_id' => auth()->id() ?? null,
            'action' => 'PERMISSION_UPDATED',
            'resource_type' => 'permissions',
            'resource_id' => $permission->permission_id,
            'metadata' => [
                'name' => $permission->name,
                'changes' => $permission->getChanges(),
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle the Permission "deleted" event.
     */
    public function deleted(Permission $permission): void
    {
        ActivityLog::create([
            'account_id' => auth()->id() ?? null,
            'action' => 'PERMISSION_DELETED',
            'resource_type' => 'permissions',
            'resource_id' => $permission->permission_id,
            'metadata' => [
                'name' => $permission->name,
                'resource_type' => $permission->resource_type,
                'action' => $permission->action,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
