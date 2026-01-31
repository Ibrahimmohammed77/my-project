<?php

namespace App\Services;

use App\Models\User;
use App\Models\DailyStat;
use Illuminate\Support\Facades\DB;

class UserStatisticsService
{
    /**
     * تحديث إحصائيات المستخدم
     */
    public static function updateUserStats(User $user): void
    {
        DB::transaction(function () use ($user) {
            $dailyStat = DailyStat::firstOrCreate(
                [
                    'stat_date' => today(),
                    'user_id' => $user->id,
                ],
                [
                    'new_users' => 0,
                    'new_photos' => 0,
                    'photo_views' => 0,
                    'card_activations' => 0,
                    'revenue' => 0,
                ]
            );

            // تحديث الإحصائيات بناءً على نوع المستخدم
            switch ($user->userTypeCode) {
                case 'customer':
                    $dailyStat->increment('new_users');
                    break;
                case 'studio_owner':
                    // تحديث إحصائيات الاستوديو
                    break;
                case 'school_owner':
                    // تحديث إحصائيات المدرسة
                    break;
            }
        });
    }

    /**
     * الحصول على إحصائيات الأداء
     */
    public static function getPerformanceStats(): array
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::active()->count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'verified_users' => User::emailVerified()->count(),
            'users_with_active_subscription' => User::hasActiveSubscription()->count(),
            'avg_login_frequency' => User::whereNotNull('last_login')
                ->select(DB::raw('AVG(DATEDIFF(NOW(), last_login)) as avg_days'))
                ->value('avg_days'),
        ];
    }
}
