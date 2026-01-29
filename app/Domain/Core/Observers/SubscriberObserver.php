<?php

namespace App\Domain\Core\Observers;

use App\Domain\Core\Models\Subscriber;
use App\Domain\Shared\Models\ActivityLog;

class SubscriberObserver
{
    /**
     * Handle the Subscriber "created" event.
     */
    public function created(Subscriber $subscriber): void
    {
        ActivityLog::create([
            'account_id' => $subscriber->account_id,
            'action' => 'SUBSCRIBER_CREATED',
            'resource_type' => 'subscribers',
            'resource_id' => $subscriber->subscriber_id,
            'metadata' => [
                'settings' => $subscriber->settings,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle the Subscriber "updated" event.
     */
    public function updated(Subscriber $subscriber): void
    {
        ActivityLog::create([
            'account_id' => $subscriber->account_id,
            'action' => 'SUBSCRIBER_UPDATED',
            'resource_type' => 'subscribers',
            'resource_id' => $subscriber->subscriber_id,
            'metadata' => [
                'changes' => $subscriber->getChanges(),
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle the Subscriber "deleted" event.
     */
    public function deleted(Subscriber $subscriber): void
    {
        ActivityLog::create([
            'account_id' => $subscriber->account_id,
            'action' => 'SUBSCRIBER_DELETED',
            'resource_type' => 'subscribers',
            'resource_id' => $subscriber->subscriber_id,
            'metadata' => [],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle the Subscriber "restored" event.
     */
    public function restored(Subscriber $subscriber): void
    {
        ActivityLog::create([
            'account_id' => $subscriber->account_id,
            'action' => 'SUBSCRIBER_RESTORED',
            'resource_type' => 'subscribers',
            'resource_id' => $subscriber->subscriber_id,
            'metadata' => [],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle the Subscriber "force deleted" event.
     */
    public function forceDeleted(Subscriber $subscriber): void
    {
        ActivityLog::create([
            'account_id' => $subscriber->account_id,
            'action' => 'SUBSCRIBER_FORCE_DELETED',
            'resource_type' => 'subscribers',
            'resource_id' => $subscriber->subscriber_id,
            'metadata' => [],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
