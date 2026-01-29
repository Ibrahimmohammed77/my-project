<?php

namespace App\Domain\Core\Observers;

use App\Domain\Core\Models\School;
use App\Domain\Shared\Models\ActivityLog;

class SchoolObserver
{
    /**
     * Handle the School "created" event.
     */
    public function created(School $school): void
    {
        ActivityLog::create([
            'account_id' => $school->account_id,
            'action' => 'SCHOOL_CREATED',
            'resource_type' => 'schools',
            'resource_id' => $school->school_id,
            'metadata' => [
                'type_id' => $school->school_type_id,
                'level_id' => $school->school_level_id,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle the School "updated" event.
     */
    public function updated(School $school): void
    {
        ActivityLog::create([
            'account_id' => $school->account_id,
            'action' => 'SCHOOL_UPDATED',
            'resource_type' => 'schools',
            'resource_id' => $school->school_id,
            'metadata' => [
                'changes' => $school->getChanges(),
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle the School "deleted" event.
     */
    public function deleted(School $school): void
    {
        ActivityLog::create([
            'account_id' => $school->account_id,
            'action' => 'SCHOOL_DELETED',
            'resource_type' => 'schools',
            'resource_id' => $school->school_id,
            'metadata' => [],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle the School "restored" event.
     */
    public function restored(School $school): void
    {
        ActivityLog::create([
            'account_id' => $school->account_id,
            'action' => 'SCHOOL_RESTORED',
            'resource_type' => 'schools',
            'resource_id' => $school->school_id,
            'metadata' => [
                'name' => $school->name,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle the School "force deleted" event.
     */
    public function forceDeleted(School $school): void
    {
        ActivityLog::create([
            'account_id' => $school->account_id,
            'action' => 'SCHOOL_FORCE_DELETED',
            'resource_type' => 'schools',
            'resource_id' => $school->school_id,
            'metadata' => [
                'name' => $school->name,
                'email' => $school->email,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
