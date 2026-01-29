<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    /**
     * تسجيل نشاط جديد
     */
    public static function logActivity(
        $userId,
        $action,
        $resourceType = null,
        $resourceId = null,
        $metadata = null,
        $ip = null,
        $userAgent = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $userId,
            'action' => $action,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'metadata' => $metadata,
            'ip_address' => $ip ?? request()->ip(),
            'user_agent' => $userAgent ?? request()->userAgent(),
        ]);
    }

    /**
     * تسجيل نشاط للمستخدم الحالي
     */
    public static function logCurrentUserActivity(
        $action,
        $resourceType = null,
        $resourceId = null,
        $metadata = null
    ): ?ActivityLog {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        return self::logActivity(
            $user->id,
            $action,
            $resourceType,
            $resourceId,
            $metadata
        );
    }

    /**
     * تسجيل نشاط النظام (بدون مستخدم)
     */
    public static function logSystemActivity(
        $action,
        $resourceType = null,
        $resourceId = null,
        $metadata = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => null,
            'action' => $action,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
