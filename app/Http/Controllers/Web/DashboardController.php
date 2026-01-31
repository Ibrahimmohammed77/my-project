<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Models\Studio;
use App\Models\School;
use App\Models\Subscription;
use App\Models\Invoice;
use App\Models\LookupValue;
use App\Models\Album;
use App\Models\Card;
use App\Models\Photo;

class DashboardController extends Controller
{
    use AuthorizesRequests;
   

    /**
     * توجيه المستخدم إلى Dashboard المناسب بناءً على أدواره.
     */
    public function index(): RedirectResponse
    {
        $user = Auth::user();

        // التحقق من أن المستخدم قد أكمل بياناته الأساسية
        if ($this->needsProfileCompletion($user)) {
            return redirect()->route('profile.completion');
        }

        // التحقق من أن المستخدم مفعل
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'حسابك غير نشط. يرجى التواصل مع الدعم.');
        }

        // توجيه المستخدم بناءً على صلاحياته
        return $this->redirectBasedOnPermissions($user);
    }

    /**
     * توجيه المستخدم إلى Dashboard المناسب بناءً على أدواره.
     */
    public function redirect(): RedirectResponse
    {
        return $this->index();
    }

    /**
     * عرض Dashboard المسؤول
     */
    public function admin(): View
    {
        $this->authorize('access-admin-dashboard');

        $stats = $this->getAdminStats();
        $statLabels = static::getAdminStatLabels();
        $controller = $this;

        return view('dashboard.admin', compact('stats', 'statLabels', 'controller'));
    }

    /**
     * عرض Dashboard صاحب الاستوديو
     */
    public function studioOwner(): View
    {
        $this->authorize('access-studio-dashboard');

        $user = Auth::user();
        $studio = $user->studio;
        $stats = $this->getStudioStats($studio);

        return view('dashboard.studio-owner', compact('user', 'studio', 'stats'));
    }

    /**
     * عرض Dashboard صاحب المدرسة
     */
    public function schoolOwner(): View
    {
        $this->authorize('access-school-dashboard');

        $user = Auth::user();
        $school = $user->school;
        $stats = $this->getSchoolStats($school);

        return view('dashboard.school-owner', compact('user', 'school', 'stats'));
    }

    /**
     * عرض Dashboard العميل
     */
    public function customer(): View
    {
        $this->authorize('access-customer-dashboard');

        $user = Auth::user();
        $customer = $user->customer;
        $stats = $this->getCustomerStats($user);

        return view('dashboard.customer', compact('user', 'customer', 'stats'));
    }

    /**
     * عرض Dashboard الزائر
     */
    public function guest(): View
    {
        $this->authorize('access-guest-dashboard');

        $user = Auth::user();

        return view('dashboard.guest', compact('user'));
    }

    /**
     * الحصول على إحصائيات المسؤول
     */
    private function getAdminStats(): array
    {
        $defaults = [
            'total_users' => 0,
            'active_users' => 0,
            'new_users_today' => 0,
            'total_studios' => 0,
            'total_schools' => 0,
            'total_subscriptions' => 0,
            'total_revenue' => 0,
            'pending_invoices' => 0,
        ];

        try {
            $paidStatusId = LookupValue::where('code', 'paid')->value('lookup_value_id');
            $pendingStatusId = LookupValue::where('code', 'pending')->value('lookup_value_id');
        } catch (\Throwable $e) {
            $paidStatusId = null;
            $pendingStatusId = null;
        }

        try {
            return [
                'total_users' => User::count(),
                'active_users' => User::active()->count(),
                'new_users_today' => User::whereDate('created_at', today())->count(),
                'total_studios' => Studio::count(),
                'total_schools' => School::count(),
                'total_subscriptions' => Subscription::active()->count(),
                'total_revenue' => $paidStatusId ? (float) Invoice::where('invoice_status_id', $paidStatusId)->sum('total_amount') : 0,
                'pending_invoices' => $pendingStatusId ? Invoice::where('invoice_status_id', $pendingStatusId)->count() : 0,
            ];
        } catch (\Throwable $e) {
            report($e);
            return $defaults;
        }
    }

    /**
     * عناوين الإحصائيات بالعربية (للوحة المسؤول)
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
     * الحصول على إحصائيات الاستوديو
     */
    private function getStudioStats($studio): array
    {
        if (!$studio) {
            return [];
        }

        return [
            'total_albums' => $studio->albums()->count(),
            'total_photos' => Album::whereHas('storageLibrary', function ($q) use ($studio) {
                $q->where('studio_id', $studio->studio_id);
            })->withCount('photos')->get()->sum('photos_count'),
            'total_customers' => $studio->customers()->count(),
            'active_cards' => Card::whereHas('albums.storageLibrary', function ($q) use ($studio) {
                $q->where('studio_id', $studio->studio_id);
            })->whereHas('status', function ($q) {
                $q->where('code', 'active');
            })->count(),
            'total_commissions' => $studio->commissions()->sum('studio_share'),
            'pending_commissions' => $studio->commissions()->whereHas('status', function ($q) {
                $q->where('code', 'pending');
            })->count(),
            'pending_photos_count' => Photo::pending()
                ->whereHas('album.storageLibrary', function($q) use ($studio) {
                    $q->where('studio_id', $studio->studio_id);
                })->count(),
        ];
    }

    /**
     * الحصول على إحصائيات المدرسة
     */
    private function getSchoolStats($school): array
    {
        if (!$school) {
            return [];
        }

        return [
            'total_students' => $school->students()->count(),
            'total_albums' => $school->albums()->count(),
            'total_photos' => $school->albums()->withCount('photos')->get()->sum('photos_count'),
            'active_cards' => $school->cards()->whereHas('status', function ($q) {
                $q->where('code', 'active');
            })->count(),
            'total_classes' => $school->classes()->count(),
        ];
    }

    /**
     * الحصول على إحصائيات العميل
     */
    private function getCustomerStats($user): array
    {
        return [
            'total_albums' => $user->albums()->count(),
            'total_photos' => $user->albums()->withCount('photos')->get()->sum('photos_count'),
            'active_cards' => $user->cards()->whereHas('status', function ($q) {
                $q->where('code', 'active');
            })->count(),
            'total_storage_used' => $user->storageAccount ? $user->storageAccount->used_space : 0,
            'storage_percentage' => $user->storageAccount ?
                ($user->storageAccount->used_space / $user->storageAccount->total_space * 100) : 0,
            'subscription_status' => $user->activeSubscription ? 'نشط' : 'غير نشط',
        ];
    }


    /**
     * توجيه المستخدم بناءً على صلاحياته
     */
    private function redirectBasedOnPermissions($user): RedirectResponse
    {
        // الأولوية 1: المسؤول
        if ($user->hasPermission('access-admin-dashboard')) {
            return redirect()->route('dashboard.admin');
        }
 
        // الأولوية 2: صاحب الاستوديو
        if ($user->hasPermission('access-studio-dashboard')) {
            return redirect()->route('dashboard.studio-owner');
        }
 
        // الأولوية 3: صاحب المدرسة
        if ($user->hasPermission('access-school-dashboard')) {
            return redirect()->route('dashboard.school-owner');
        }
 
        // الأولوية 4: الموظف
        if ($user->hasPermission('access-final-user-dashboard')) {
            return redirect()->route('dashboard.final_user');
        }
 
        // الأولوية 5: المحرر
        if ($user->hasPermission('access-editor-dashboard')) {
            return redirect()->route('dashboard.editor');
        }
 
        // الأولوية 6: العميل
        if ($user->hasPermission('access-customer-dashboard')) {
            return redirect()->route('dashboard.customer');
        }

        // الافتراضي: الزائر
        return redirect()->route('dashboard.guest');
    }

    /**
     * التحقق مما إذا كان المستخدم بحاجة إلى إكمال ملفه الشخصي
     */
    private function needsProfileCompletion($user): bool
    {
        // استثناء المشرفين من هذا التحقق
        if ($user->hasPermission('access-admin-dashboard')) {
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

        // إذا كان صاحب استوديو ولم يكن لديه استوديو
        if ($user->hasRole('studio_owner') && !$user->studio()->exists()) {
            return true;
        }

        // إذا كان صاحب مدرسة ولم يكن لديه مدرسة
        if ($user->hasRole('school_owner') && !$user->school()->exists()) {
            return true;
        }

        // إذا كان عميلاً ولم يكن لديه ملف عميل
        if ($user->hasRole('customer') && !$user->customer()->exists()) {
            return true;
        }

        return false;
    }

    /**
     * الحصول على لون الإحصائية
     */
    public function getStatColor($key): string
    {
        $colors = [
            'total_users' => 'bg-blue-500',
            'active_users' => 'bg-green-500',
            'new_users_today' => 'bg-purple-500',
            'total_studios' => 'bg-indigo-500',
            'total_schools' => 'bg-red-500',
            'total_subscriptions' => 'bg-yellow-500',
            'total_revenue' => 'bg-emerald-500',
            'pending_invoices' => 'bg-orange-500',
        ];

        return $colors[$key] ?? 'bg-gray-500';
    }

    /**
     * الحصول على أيقونة الإحصائية
     */
    public function getStatIcon($key): string
    {
        $icons = [
            'total_users' => 'fas fa-users',
            'active_users' => 'fas fa-user-check',
            'new_users_today' => 'fas fa-user-plus',
            'total_studios' => 'fas fa-building',
            'total_schools' => 'fas fa-school',
            'total_subscriptions' => 'fas fa-membership',
            'total_revenue' => 'fas fa-dollar-sign',
            'pending_invoices' => 'fas fa-file-invoice-dollar',
        ];

        return $icons[$key] ?? 'fas fa-chart-bar';
    }
}
