<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\LookupValue;
use App\UseCases\Admin\Subscription\GrantSubscriptionUseCase;
use App\UseCases\Admin\Subscription\ListSubscriptionsUseCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    protected $grantSubscriptionUseCase;
    protected $listSubscriptionsUseCase;

    public function __construct(
        GrantSubscriptionUseCase $grantSubscriptionUseCase,
        ListSubscriptionsUseCase $listSubscriptionsUseCase
    ) {
        $this->grantSubscriptionUseCase = $grantSubscriptionUseCase;
        $this->listSubscriptionsUseCase = $listSubscriptionsUseCase;
    }

    /**
     * Display a listing of the subscriptions.
     */
    public function index(Request $request)
    {
        Gate::authorize('manage_subscriptions');

        $filters = $request->only(['search', 'plan_id', 'status_id']);
        $subscriptions = $this->listSubscriptionsUseCase->execute($filters, $request->get('per_page', 15));

        $plans = Plan::where('is_active', true)->get();
        
        $statuses = LookupValue::whereHas('master', function ($q) {
            $q->where('code', 'SUBSCRIPTION_STATUS');
        })->where('is_active', true)->get();

        if ($request->wantsJson()) {
            return $this->paginatedResponse($subscriptions, 'subscriptions');
        }

        return view('spa.subscriptions.index', compact('subscriptions', 'plans', 'statuses', 'filters'));
    }

    /**
     * Store a new subscription.
     */
    public function store(Request $request)
    {
        Gate::authorize('manage_subscriptions');

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,plan_id',
            'billing_cycle' => 'required|in:monthly,yearly',
            'auto_renew' => 'boolean',
        ]);

        try {
            $subscription = $this->grantSubscriptionUseCase->execute($validated);
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم منح الخطة بنجاح',
                    'data' => ['subscription' => $subscription->load(['user', 'plan', 'status'])]
                ], 201);
            }

            return redirect()->back()->with('success', 'تم منح الخطة بنجاح');
        } catch (\Exception $e) {
            Log::error('Error granting subscription: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء منح الخطة',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'حدث خطأ أثناء منح الخطة: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified subscription.
     */
    public function destroy(Subscription $subscription)
    {
        Gate::authorize('manage_subscriptions');

        try {
            $subscription->delete();
            
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم حذف الاشتراك بنجاح'
                ]);
            }
            
            return redirect()->back()->with('success', 'تم حذف الاشتراك بنجاح');
        } catch (\Exception $e) {
            Log::error('Error deleting subscription: ' . $e->getMessage());
            
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء حذف الاشتراك',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'حدث خطأ أثناء حذف الاشتراك');
        }
    }
}
