<?php

namespace App\Services\Admin;

use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use App\Models\Plan;
use App\Models\User;
use App\Models\LookupValue;
use App\Models\ActivityLog;
use App\Helpers\StorageLibraryHelper;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    public function __construct(
        protected SubscriptionRepositoryInterface $subscriptionRepository
    ) {}

    /**
     * عرض قائمة الاشتراكات
     */
    public function listSubscriptions(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->subscriptionRepository->listByAdmin($filters, $perPage);
    }

    /**
     * منح اشتراك جديد
     */
    public function grantSubscription(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::findOrFail($data['user_id']);
            $plan = Plan::findOrFail($data['plan_id']);

            $activeStatus = LookupValue::where('code', 'ACTIVE')
                ->whereHas('master', function($q) {
                    $q->where('code', 'SUBSCRIPTION_STATUS');
                })->first();

            // حساب التواريخ
            $startDate = now();
            $endDate = $data['billing_cycle'] === 'yearly' 
                ? now()->addYear() 
                : now()->addMonth();
            
            $renewalDate = $endDate->copy()->subDays(7);

            $subscriptionData = [
                'user_id' => $user->id,
                'plan_id' => $plan->plan_id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'renewal_date' => $renewalDate,
                'auto_renew' => $data['auto_renew'] ?? true,
                'subscription_status_id' => $activeStatus->lookup_value_id,
            ];

            $subscription = $this->subscriptionRepository->store($subscriptionData);

            // إنشاء مكتبة تخزين تلقائيًا للمدارس والاستوديوهات
            try {
                if ($user->hasRole('school-owner') && $user->school) {
                    StorageLibraryHelper::ensureStorageLibraryForSchool($user->school);
                } 
            } catch (\Exception $e) {
                // Log error but don't fail the subscription
                \Log::warning('Failed to create storage library for user ' . $user->id . ': ' . $e->getMessage());
            }

            // تسجيل النشاط
            ActivityLog::logCurrent(
                'subscription_granted',
                User::class,
                $user->id,
                ['plan' => $plan->name, 'end_date' => $endDate->toDateString()]
            );

            return $subscription;
        });
    }

    /**
     * تحديث اشتراك
     */
    public function updateSubscription($subscription, array $data)
    {
        return DB::transaction(function () use ($subscription, $data) {
            $updatedSubscription = $this->subscriptionRepository->update($subscription, $data);
            
            $user = $subscription->user;
            
            // إنشاء مكتبة تخزين تلقائيًا للمدارس والاستوديوهات إذا لم تكن موجودة
            try {
                if ($user->hasRole('school-owner') && $user->school) {
                    StorageLibraryHelper::ensureStorageLibraryForSchool($user->school);
                }
            } catch (\Exception $e) {
                // Log error but don't fail the update
                \Log::warning('Failed to create storage library for user ' . $user->id . ': ' . $e->getMessage());
            }

            // تسجيل النشاط
            ActivityLog::logCurrent(
                'subscription_updated',
                \App\Models\Subscription::class,
                $subscription->subscription_id,
                ['changes' => $data]
            );

            return $updatedSubscription;
        });
    }

    /**
     * حذف اشتراك
     */
    public function deleteSubscription($subscription): bool
    {
        return DB::transaction(function () use ($subscription) {
            // تسجيل النشاط قبل الحذف
            ActivityLog::logCurrent(
                'subscription_deleted',
                \App\Models\Subscription::class,
                $subscription->subscription_id,
                ['plan' => $subscription->plan->name, 'user' => $subscription->user->name]
            );

            return $this->subscriptionRepository->delete($subscription);
        });
    }
}