<?php

namespace App\Observers;

use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\LookupValue;
use App\Events\UserCreated;
use App\Events\UserUpdated;
use App\Events\UserDeleted;
use App\Events\UserRestored;
use App\Services\UserCacheService;

class UserObserver
{
    /**
     * معالجة الحدث قبل الإنشاء
     */
    public function creating(User $user): void
    {
        // تعيين معرف حالة افتراضي إذا لم يتم تحديده
        if (!$user->user_status_id) {
            $status = LookupValue::where('code', 'active')->first();
            $user->user_status_id = $status->lookup_value_id ?? null;
        }

        // إنشاء كود تحقق
        $user->verification_code = rand(100000, 999999);
        $user->verification_expiry = now()->addHours(24);
    }

    /**
     * معالجة الحدث بعد الإنشاء
     */
    public function created(User $user): void
    {
        // إنشاء حساب تخزين افتراضي
        $user->storageAccount()->create([
            'total_space' => 0,
            'used_space' => 0,
            'status' => 'active',
        ]);

        // تسجيل النشاط
        ActivityLog::log(
            $user->id,
            'create',
            'user',
            $user->id,
            ['email' => $user->email]
        );

        // إرسال إشعار ترحيبي
        $notificationType = LookupValue::where('code', 'welcome')->first();

        if ($notificationType) {
            Notification::create([
                'user_id' => $user->id,
                'title' => 'مرحباً بك!',
                'message' => 'شكراً لتسجيلك في منصتنا.',
                'notification_type_id' => $notificationType->lookup_value_id,
                'metadata' => ['welcome' => true],
            ]);
        }

        // تشغيل حدث UserCreated
        event(new UserCreated($user));
    }

    /**
     * معالجة الحدث قبل التحديث
     */
    public function updating(User $user): void
    {
        // مسح ذاكرة التخزين المؤقت للتغييرات المهمة
        if ($user->isDirty(['user_type_id', 'user_status_id', 'is_active'])) {
            UserCacheService::flushUserCache($user->id);
            cache()->forget("user_{$user->id}_permissions");
        }

        // تسجيل التغييرات المهمة
        $this->logImportantChanges($user);
    }

    /**
     * معالجة الحدث بعد التحديث
     */
    public function updated(User $user): void
    {
        // تسجيل النشاط
        $changes = $user->getChanges();
        unset($changes['last_login']); // لا نحتاج لتسجيل تحديث last_login

        if (!empty($changes)) {
            ActivityLog::log(
                $user->id,
                'update',
                'user',
                $user->id,
                ['changes' => $changes]
            );
        }

        // تحديث التخزين المؤقت
        UserCacheService::put($user);

        // تشغيل حدث UserUpdated
        event(new UserUpdated($user, $changes));
    }

    /**
     * معالجة الحدث قبل الحذف
     */
    public function deleting(User $user): void
    {
        // مسح جميع ذاكرات التخزين المؤقت
        UserCacheService::flushUserCache($user->id);

        // تعطيل المستخدم أولاً (إذا لم يكن محذوفاً نهائياً)
        if (!$user->isForceDeleting()) {
            $user->update(['is_active' => false]);
        }
    }

    /**
     * معالجة الحدث بعد الحذف
     */
    public function deleted(User $user): void
    {
        // تسجيل النشاط
        ActivityLog::log(
            $user->id,
            $user->isForceDeleting() ? 'force_delete' : 'delete',
            'user',
            $user->id,
            ['email' => $user->email]
        );

        // تشغيل حدث UserDeleted
        event(new UserDeleted($user));
    }

    /**
     * معالجة الحدث بعد الاستعادة
     */
    public function restored(User $user): void
    {
        // تسجيل النشاط
        ActivityLog::log(
            $user->id,
            'restore',
            'user',
            $user->id,
            ['email' => $user->email]
        );

        // تحديث التخزين المؤقت
        UserCacheService::put($user);

        // تنشيط المستخدم بعد الاستعادة
        $user->update(['is_active' => true]);

        // تشغيل حدث UserRestored
        event(new UserRestored($user));
    }

    /**
     * تسجيل التغييرات المهمة
     */
    private function logImportantChanges(User $user): void
    {
        $importantFields = ['user_status_id', 'user_type_id', 'is_active', 'email_verified', 'phone_verified'];

        foreach ($importantFields as $field) {
            if ($user->isDirty($field)) {
                $oldValue = $user->getOriginal($field);
                $newValue = $user->$field;

                ActivityLog::log(
                    $user->id,
                    "{$field}_changed",
                    'user',
                    $user->id,
                    [
                        'old_value' => $oldValue,
                        'new_value' => $newValue,
                        'field' => $field,
                    ]
                );
            }
        }
    }
}
