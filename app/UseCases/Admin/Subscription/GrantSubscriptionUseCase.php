<?php

namespace App\UseCases\Admin\Subscription;

use App\Models\Subscription;
use App\Models\Plan;
use App\Models\User;
use App\Models\LookupValue;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Exception;

class GrantSubscriptionUseCase
{
    /**
     * Execute the use case.
     */
    public function execute(array $data): Subscription
    {
        return DB::transaction(function () use ($data) {
            $user = User::findOrFail($data['user_id']);
            $plan = Plan::findOrFail($data['plan_id']);

            $activeStatus = LookupValue::where('code', 'ACTIVE')
                ->whereHas('master', function($q) {
                    $q->where('code', 'SUBSCRIPTION_STATUS');
                })->first();

            // Calculate dates
            $startDate = now();
            $endDate = $data['billing_cycle'] === 'yearly' 
                ? now()->addYear() 
                : now()->addMonth();
            
            $renewalDate = $endDate->copy()->subDays(7);

            $subscription = Subscription::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'plan_id' => $plan->plan_id,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'renewal_date' => $renewalDate,
                    'auto_renew' => $data['auto_renew'] ?? true,
                    'subscription_status_id' => $activeStatus->lookup_value_id,
                ]
            );

            // Log activity
            ActivityLog::logCurrent(
                'subscription_granted',
                User::class,
                $user->id,
                ['plan' => $plan->name, 'end_date' => $endDate->toDateString()]
            );

            return $subscription;
        });
    }
}
