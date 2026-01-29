<?php

namespace App\Observers;

use App\Models\Subscription;
use App\Models\ActivityLog;

class SubscriptionObserver
{
    public function created(Subscription $subscription): void
    {
        ActivityLog::logActivity(
            $subscription->user_id,
            'subscribe',
            'subscription',
            $subscription->id,
            ['plan_id' => $subscription->plan_id]
        );
    }

    public function updated(Subscription $subscription): void
    {
        if ($subscription->isDirty('status_id')) {
            ActivityLog::logActivity(
                $subscription->user_id,
                'update',
                'subscription',
                $subscription->id,
                ['status' => $subscription->status_id]
            );
        }
    }
}
