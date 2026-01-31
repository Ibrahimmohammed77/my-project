<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\Support\Facades\{Auth, Gate};
use Illuminate\View\View;

class DashboardController extends Controller
{
  protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Redirect user to appropriate dashboard.
     */
    public function index(): RedirectResponse
    {
        $user = Auth::user();

        // Check account status
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'حسابك غير نشط. يرجى التواصل مع الدعم.');
        }

        // Check profile completion
        if ($this->dashboardService->needsProfileCompletion($user)) {
            return redirect()->route('profile.completion');
        }

        // Redirect based on permissions
        $route = $this->dashboardService->redirectBasedOnPermissions($user);
        return redirect()->route($route);
    }

    /**
     * Alias for index method.
     */
    public function redirect(): RedirectResponse
    {
        return $this->index();
    }

    // ==================== ADMIN DASHBOARD ====================

    /**
     * Display admin dashboard.
     */
    public function admin(): View
    {
        $this->authorize('access-admin-dashboard');

        $stats = $this->dashboardService->getAdminStats();
        $recentActivities = $this->dashboardService->getRecentActivities(10);

        return view('dashboard.admin', [
            'stats' => $stats,
            'recentActivities' => $recentActivities,
            'dashboardService' => $this->dashboardService,
        ]);
    }

    /**
     * Get activity icon based on action type.
     */
    private function getActivityIcon($activity): string
    {
        $action = $activity->action ?? '';

        $icons = [
            'login' => 'sign-in-alt',
            'logout' => 'sign-out-alt',
            'create' => 'plus-circle',
            'update' => 'edit',
            'delete' => 'trash-alt',
            'user_created' => 'user-plus',
            'user_updated' => 'user-edit',
            'user_deleted' => 'user-minus',
            'email_verified' => 'envelope-open',
            'phone_verified' => 'phone-alt',
        ];

        return $icons[$action] ?? 'bell';
    }

    // ==================== STUDIO OWNER DASHBOARD ====================

    /**
     * Display studio owner dashboard.
     */
    public function studioOwner(): View
    {
        $this->authorize('access-studio-dashboard');

        $user = Auth::user();
        $studio = $user->studio;
        $stats = $studio ? $this->dashboardService->getStudioStats($studio) : [];

        return view('dashboard.studio-owner', compact('user', 'studio', 'stats'));
    }

    // ==================== SCHOOL OWNER DASHBOARD ====================

    /**
     * Display school owner dashboard.
     */
    public function schoolOwner(): View
    {
        $this->authorize('access-school-dashboard');

        $user = Auth::user();
        $school = $user->school;
        $stats = $school ? $this->dashboardService->getSchoolStats($school) : [];

        return view('dashboard.school-owner', compact('user', 'school', 'stats'));
    }

    // ==================== CUSTOMER DASHBOARD ====================

    /**
     * Display customer dashboard.
     */
    public function customer(): View
    {
        $this->authorize('access-customer-dashboard');

        $user = Auth::user();
        $customer = $user->customer;
        $stats = $this->dashboardService->getCustomerStats($user);

        return view('dashboard.customer', compact('user', 'customer', 'stats'));
    }

    // ==================== FINAL USER DASHBOARD ====================

    /**
     * Display final user (employee) dashboard.
     */
    public function finalUser(): View
    {
        $this->authorize('access-final-user-dashboard');

        $user = Auth::user();
        return view('dashboard.final-user', compact('user'));
    }

    // ==================== EDITOR DASHBOARD ====================

    /**
     * Display editor dashboard.
     */
    public function editor(): View
    {
        $this->authorize('access-editor-dashboard');

        $user = Auth::user();
        return view('dashboard.editor', compact('user'));
    }

    // ==================== GUEST DASHBOARD ====================

    /**
     * Display guest dashboard.
     */
    public function guest(): View
    {
        $this->authorize('access-guest-dashboard');

        $user = Auth::user();
        return view('dashboard.guest', compact('user'));
    }

    // ==================== AJAX ENDPOINTS ====================

    /**
     * Get admin stats via AJAX.
     */
    public function getAdminStatsJson(): \Illuminate\Http\JsonResponse
    {
        $this->authorize('access-admin-dashboard');

        $stats = $this->dashboardService->getAdminStats();
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get studio stats via AJAX.
     */
    public function getStudioStatsJson(): \Illuminate\Http\JsonResponse
    {
        $this->authorize('access-studio-dashboard');

        $user = Auth::user();
        $studio = $user->studio;
        $stats = $studio ? $this->dashboardService->getStudioStats($studio) : [];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get school stats via AJAX.
     */
    public function getSchoolStatsJson(): \Illuminate\Http\JsonResponse
    {
        $this->authorize('access-school-dashboard');

        $user = Auth::user();
        $school = $user->school;
        $stats = $school ? $this->dashboardService->getSchoolStats($school) : [];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
