<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\LookupValue;
use App\Services\Admin\SubscriptionService;
use App\Traits\HasApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    use HasApiResponse;

    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Display a listing of the subscriptions.
     */
    public function index(Request $request)
    {
        Gate::authorize('manage_subscriptions');

        $filters = $request->only(['search', 'plan_id', 'status_id']);
        $subscriptions = $this->subscriptionService->listSubscriptions($filters, $request->get('per_page', 15));

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
            $subscription = $this->subscriptionService->grantSubscription($validated);
            
            if ($request->wantsJson()) {
                return $this->successResponse(
                    ['subscription' => $subscription->load(['user', 'plan', 'status'])],
                    'تم منح الاشتراك بنجاح',
                    201
                );
            }

            return redirect()->back()->with('success', 'تم منح الاشتراك بنجاح');
        } catch (\Exception $e) {
            Log::error('Error granting subscription: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return $this->errorResponse('حدث خطأ أثناء منح الاشتراك', 500, $e->getMessage());
            }

            return redirect()->back()->with('error', 'حدث خطأ أثناء منح الاشتراك: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified subscription.
     */
    public function destroy(Subscription $subscription)
    {
        Gate::authorize('manage_subscriptions');

        try {
            $this->subscriptionService->deleteSubscription($subscription);
            
            if (request()->wantsJson()) {
                return $this->successResponse(null, 'تم حذف الاشتراك بنجاح');
            }
            
            return redirect()->back()->with('success', 'تم حذف الاشتراك بنجاح');
        } catch (\Exception $e) {
            Log::error('Error deleting subscription: ' . $e->getMessage());
            
            if (request()->wantsJson()) {
                return $this->errorResponse('حدث خطأ أثناء حذف الاشتراك', 500, $e->getMessage());
            }
            
            return back()->with('error', 'حدث خطأ أثناء حذف الاشتراك');
        }
    }

    /**
     * Update the specified subscription.
     */
    public function update(Request $request, Subscription $subscription)
    {
        Gate::authorize('manage_subscriptions');

        $validated = $request->validate([
            'plan_id' => 'sometimes|exists:plans,plan_id',
            'billing_cycle' => 'sometimes|in:monthly,yearly',
            'auto_renew' => 'boolean',
            'status_id' => 'sometimes|exists:lookup_values,lookup_value_id',
        ]);

        try {
            $updatedSubscription = $this->subscriptionService->updateSubscription($subscription, $validated);
            
            if ($request->wantsJson()) {
                return $this->successResponse(
                    ['subscription' => $updatedSubscription->load(['user', 'plan', 'status'])],
                    'تم تحديث الاشتراك بنجاح'
                );
            }

            return redirect()->back()->with('success', 'تم تحديث الاشتراك بنجاح');
        } catch (\Exception $e) {
            Log::error('Error updating subscription: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return $this->errorResponse('حدث خطأ أثناء تحديث الاشتراك', 500, $e->getMessage());
            }

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث الاشتراك');
        }
    }
}