<?php

namespace App\Domain\Core\Observers;

use App\Domain\Core\Models\Studio;
use App\Domain\Shared\Models\ActivityLog;

class StudioObserver
{
    /**
     * Handle the Studio "created" event.
     */
    public function created(Studio $studio): void
    {
        ActivityLog::create([
            'account_id' => $studio->account_id,
            'action' => 'STUDIO_CREATED',
            'resource_type' => 'studios',
            'resource_id' => $studio->studio_id,
            'metadata' => [],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle the Studio "updated" event.
     */
    public function updated(Studio $studio): void
    {
        ActivityLog::create([
            'account_id' => $studio->account_id,
            'action' => 'STUDIO_UPDATED',
            'resource_type' => 'studios',
            'resource_id' => $studio->studio_id,
            'metadata' => [
                'changes' => $studio->getChanges(),
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle the Studio "deleted" event.
     */
    public function deleted(Studio $studio): void
    {
        ActivityLog::create([
            'account_id' => $studio->account_id,
            'action' => 'STUDIO_DELETED',
            'resource_type' => 'studios',
            'resource_id' => $studio->studio_id,
            'metadata' => [],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle the Studio "restored" event.
     */
    public function restored(Studio $studio): void
    {
        ActivityLog::create([
            'account_id' => $studio->account_id,
            'action' => 'STUDIO_RESTORED',
            'resource_type' => 'studios',
            'resource_id' => $studio->studio_id,
            'metadata' => [
                'name' => $studio->name,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle the Studio "force deleted" event.
     */
    public function forceDeleted(Studio $studio): void
    {
        ActivityLog::create([
            'account_id' => $studio->account_id,
            'action' => 'STUDIO_FORCE_DELETED',
            'resource_type' => 'studios',
            'resource_id' => $studio->studio_id,
            'metadata' => [
                'name' => $studio->name,
                'email' => $studio->email,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
