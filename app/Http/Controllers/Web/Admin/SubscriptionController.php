<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use App\UseCases\Admin\Subscription\GrantSubscriptionUseCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SubscriptionController extends Controller
{
    protected $grantSubscriptionUseCase;

    public function __construct(GrantSubscriptionUseCase $grantSubscriptionUseCase)
    {
        $this->grantSubscriptionUseCase = $grantSubscriptionUseCase;
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
            $this->grantSubscriptionUseCase->execute($validated);
            return redirect()->back()->with('success', 'تم منح الخطة بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء منح الخطة: ' . $e->getMessage());
        }
    }
}
