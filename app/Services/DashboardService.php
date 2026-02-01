<?php

namespace App\Services;

use App\Models\{ActivityLog, User, Studio, School, Subscription, Invoice, LookupValue, Album, Card, Photo};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public function __construct()
    {
        //
    }


    /**
     * Get studio owner dashboard statistics.
     */
    public function getStudioStats(Studio $studio): array
    {
        return Cache::remember("studio_stats_{$studio->studio_id}", 180, function () use ($studio) {
            return [
                'total_albums' => $studio->albums()->count(),
                'total_photos' => $this->countStudioPhotos($studio),
                'total_customers' => $studio->customers()->count(),
                'active_cards' => $this->countActiveStudioCards($studio),
                'total_commissions' => $studio->commissions()->sum('studio_share'),
                'pending_commissions' => $studio->commissions()
                    ->whereHas('status', fn($q) => $q->where('code', 'pending'))
                    ->count(),
                'pending_photos_count' => $this->countPendingStudioPhotos($studio),
            ];
        });
    }

    /**
     * Get school owner dashboard statistics.
     */
    public function getSchoolStats(School $school): array
    {
        return Cache::remember("school_stats_{$school->school_id}", 180, function () use ($school) {
            return [
                'total_students' => $school->students()->count(),
                'total_albums' => $school->albums()->count(),
                'total_photos' => $this->countSchoolPhotos($school),
                'active_cards' => $this->countActiveSchoolCards($school),
                'total_classes' => $school->classes()->count(),
            ];
        });
    }

    /**
     * Get customer dashboard statistics.
     */
    public function getCustomerStats(User $user): array
    {
        return [
            'total_albums' => $user->albums()->count(),
            'total_photos' => $user->albums()->withCount('photos')->get()->sum('photos_count'),
            'active_cards' => $user->cards()
                ->whereHas('status', fn($q) => $q->where('code', 'active'))
                ->count(),
            'total_storage_used' => $user->storageAccount?->used_space ?? 0,
            'storage_percentage' => $user->storageAccount
                ? ($user->storageAccount->used_space / $user->storageAccount->total_space * 100)
                : 0,
            'subscription_status' => $user->activeSubscription ? 'نشط' : 'غير نشط',
        ];
    }

    /**
     * Check if user needs profile completion.
     */
    public function needsProfileCompletion(User $user): bool
    {
        // استثناء المشرفين من هذا التحقق
        if ($user->can('access-admin-dashboard')) {
            return false;
        }

        // التحقق من المعلومات الأساسية
        if (empty($user->name) || empty($user->email) || empty($user->phone)) {
            return true;
        }

        // التحقق من نوع المستخدم
        if (!$user->user_type_id) {
            return true;
        }

        // التحقق من وجود الملف الشخصي المناسب حسب الدور
        return match (true) {
            $user->hasRole('studio_owner') => !$user->studio()->exists(),
            $user->hasRole('school_owner') => !$user->school()->exists(),
            $user->hasRole('customer') => !$user->customer()->exists(),
            default => false,
        };
    }

    /**
     * Get admin stat labels in Arabic.
     */
    public static function getAdminStatLabels(): array
    {
        return [
            'total_users' => 'إجمالي المستخدمين',
            'active_users' => 'المستخدمون النشطون',
            'new_users_today' => 'مستخدمون جدد اليوم',
            'total_studios' => 'إجمالي الاستوديوهات',
            'total_schools' => 'إجمالي المدارس',
            'total_subscriptions' => 'الاشتراكات النشطة',
            'total_revenue' => 'إجمالي الإيرادات',
            'pending_invoices' => 'فواتير معلقة',
        ];
    }


    /**
     * Redirect user based on permissions.
     */
    public function redirectBasedOnPermissions(User $user): string
    {
        $redirects = [
            'access-admin-dashboard' => 'dashboard.admin',
            'access-studio-dashboard' => 'dashboard.studio-owner',
            'access-school-dashboard' => 'dashboard.school-owner',
            'access-final-user-dashboard' => 'dashboard.final_user',
            'access-editor-dashboard' => 'dashboard.editor',
            'access-customer-dashboard' => 'dashboard.customer',
        ];

        foreach ($redirects as $permission => $route) {
            if ($user->can($permission)) {
                return $route;
            }
        }

        return 'dashboard.guest';
    }



    /**
     * Count photos in a studio.
     */
    private function countStudioPhotos(Studio $studio): int
    {
        return Album::whereHas('storageLibrary', function ($q) use ($studio) {
            $q->where('studio_id', $studio->studio_id);
        })->withCount('photos')->get()->sum('photos_count');
    }
/**
    **
     * Get recent activities for dashboard.
     */
    public function getRecentActivities(int $limit = 10)
    {
        $user = Auth::user();

        if (!$user) {
            return collect();
        }

        return Cache::remember("user_{$user->id}_recent_activities", 60, function () use ($user, $limit) {
            return ActivityLog::query()
                ->where('user_id', $user->id)
                ->orWhere(function ($q) use ($user) {
                    $q->where('resource_id', $user->id)
                      ->where('resource_type', 'user');
                })
                ->with('user:id,name')
                ->latest()
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Count active cards in a studio.
     */
    private function countActiveStudioCards(Studio $studio): int
    {
        return Card::whereHas('albums.storageLibrary', function ($q) use ($studio) {
            $q->where('studio_id', $studio->studio_id);
        })->whereHas('status', function ($q) {
            $q->where('code', 'active');
        })->count();
    }

    /**
     * Count pending photos in a studio.
     */
    private function countPendingStudioPhotos(Studio $studio): int
    {
        return Photo::pending()
            ->whereHas('album.storageLibrary', function($q) use ($studio) {
                $q->where('studio_id', $studio->studio_id);
            })->count();
    }

    /**
     * Count photos in a school.
     */
    private function countSchoolPhotos(School $school): int
    {
        return $school->albums()->withCount('photos')->get()->sum('photos_count');
    }

    /**
     * Count active cards in a school.
     */
    private function countActiveSchoolCards(School $school): int
    {
        return $school->cards()
            ->whereHas('status', function ($q) {
                $q->where('code', 'active');
            })->count();
    }


    /**
     * Get admin dashboard statistics.
     */
    public function getAdminStats(): array
    {
        return Cache::remember('admin_dashboard_stats', 300, function () {
            try {
                $paidStatusId = LookupValue::where('code', 'PAID')->value('lookup_value_id');
                $pendingStatusId = LookupValue::where('code', 'PENDING')->value('lookup_value_id');

                return [
                    'total_users' => User::count(),
                    'active_users' => User::active()->count(),
                    'new_users_today' => User::whereDate('created_at', today())->count(),
                    'total_studios' => Studio::count(),
                    'total_schools' => School::count(),
                    'total_subscriptions' => Subscription::activeSubscription()->count(),
                    'total_revenue' => $paidStatusId ? (float) Invoice::where('invoice_status_id', $paidStatusId)->sum('total_amount') : 0,
                    'pending_invoices' => $pendingStatusId ? Invoice::where('invoice_status_id', $pendingStatusId)->count() : 0,
                ];
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Dashboard Stats Error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
                report($e);
                return $this->getDefaultAdminStats();
            }
        });
    }

    /**
     * Get stat display configuration.
     */
    public function getStatDisplay(string $key): array
    {
        $configs = [
            'total_users' => ['color' => 'bg-blue-500', 'icon' => 'fas fa-users', 'label' => 'إجمالي المستخدمين'],
            'active_users' => ['color' => 'bg-green-500', 'icon' => 'fas fa-user-check', 'label' => 'المستخدمون النشطون'],
            'new_users_today' => ['color' => 'bg-purple-500', 'icon' => 'fas fa-user-plus', 'label' => 'مستخدمون جدد اليوم'],
            'total_studios' => ['color' => 'bg-indigo-500', 'icon' => 'fas fa-building', 'label' => 'إجمالي الاستوديوهات'],
            'total_schools' => ['color' => 'bg-red-500', 'icon' => 'fas fa-school', 'label' => 'إجمالي المدارس'],
            'total_subscriptions' => ['color' => 'bg-yellow-500', 'icon' => 'fas fa-membership', 'label' => 'الاشتراكات النشطة'],
            'total_revenue' => ['color' => 'bg-emerald-500', 'icon' => 'fas fa-dollar-sign', 'label' => 'إجمالي الإيرادات'],
            'pending_invoices' => ['color' => 'bg-orange-500', 'icon' => 'fas fa-file-invoice-dollar', 'label' => 'فواتير معلقة'],
        ];

        return $configs[$key] ?? ['color' => 'bg-gray-500', 'icon' => 'fas fa-chart-bar', 'label' => $key];
    }

    /**
     * Get default admin stats in case of error.
     */
    private function getDefaultAdminStats(): array
    {
        return [
            'total_users' => 0,
            'active_users' => 0,
            'new_users_today' => 0,
            'total_studios' => 0,
            'total_schools' => 0,
            'total_subscriptions' => 0,
            'total_revenue' => 0,
            'pending_invoices' => 0,
        ];
    }

    /**
     * Format value for display.
     */
    public function formatValue(string $key, $value): string
    {
        if ($key === 'total_revenue') {
            return number_format($value, 0) . ' ريال';
        }

        return number_format($value);
    }
}

