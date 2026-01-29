<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\User;

class UserCacheService
{
    const TTL = 3600; // ساعة واحدة
    const USER_TAG_PREFIX = 'user_';

    /**
     * الحصول على بيانات المستخدم من الذاكرة المؤقتة
     */
    public static function get($userId)
    {
        $key = self::USER_TAG_PREFIX . $userId;

        return Cache::tags([$key])->remember($key, self::TTL, function () use ($userId) {
            return User::withCommonRelations()->find($userId);
        });
    }

    /**
     * تحديث بيانات المستخدم في الذاكرة المؤقتة
     */
    public static function put(User $user): void
    {
        $key = self::USER_TAG_PREFIX . $user->id;
        Cache::tags([$key])->put($key, $user, self::TTL);
    }

    /**
     * مسح بيانات المستخدم من الذاكرة المؤقتة
     */
    public static function forget($userId): void
    {
        $key = self::USER_TAG_PREFIX . $userId;
        Cache::tags([$key])->flush();
    }

    /**
     * الحصول على صلاحيات المستخدم من الذاكرة المؤقتة
     */
    public static function getPermissions($userId)
    {
        $key = self::USER_TAG_PREFIX . $userId . '_permissions';

        return Cache::tags([self::USER_TAG_PREFIX . $userId])->remember($key, 300, function () use ($userId) {
            $user = User::find($userId);
            return $user ? $user->permissions() : collect();
        });
    }

    /**
     * مسح جميع ذاكرات التخزين المؤقتة للمستخدم
     */
    public static function flushUserCache($userId): void
    {
        Cache::tags([self::USER_TAG_PREFIX . $userId])->flush();
    }
}
