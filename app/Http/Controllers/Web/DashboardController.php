<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

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
        $controller = $this;

        return view('dashboard.admin', compact('stats', 'controller'));
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
        return [
            'total_users' => \App\Models\User::count(),
            'active_users' => \App\Models\User::active()->count(),
            'new_users_today' => \App\Models\User::whereDate('created_at', today())->count(),
            'total_studios' => \App\Models\Studio::count(),
            'total_schools' => \App\Models\School::count(),
            'total_subscriptions' => \App\Models\Subscription::active()->count(),
            'total_revenue' => \App\Models\Invoice::where('invoice_status_id', function ($query) {
                $query->select('lookup_value_id')
                    ->from('lookup_values')
                    ->where('code', 'paid');
            })->sum('total_amount'),
            'pending_invoices' => \App\Models\Invoice::where('invoice_status_id', function ($query) {
                $query->select('lookup_value_id')
                    ->from('lookup_values')
                    ->where('code', 'pending');
            })->count(),
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
            'total_photos' => \App\Models\Album::whereHas('storageLibrary', function ($q) use ($studio) {
                $q->where('studio_id', $studio->studio_id);
            })->withCount('photos')->get()->sum('photos_count'),
            'total_customers' => $studio->customers()->count(),
            'active_cards' => \App\Models\Card::whereHas('albums.storageLibrary', function ($q) use ($studio) {
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
        if ($user->hasPermission('access-employee-dashboard')) {
            return redirect()->route('dashboard.employee');
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
