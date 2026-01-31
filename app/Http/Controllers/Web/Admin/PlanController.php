<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\{CreatePlanRequest, UpdatePlanRequest};
use App\Services\Admin\PlanService;
use App\Models\Plan;
use App\Traits\HasApiResponse;
use Illuminate\Http\{Request, JsonResponse, RedirectResponse};
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class PlanController extends Controller
{
    use HasApiResponse;

    public function __construct(
        protected PlanService $planService
    ) {}

    /**
     * Display a listing of plans.
     */
    public function index(Request $request): View|JsonResponse
    {
        if (!Gate::allows('manage_plans') && !Gate::allows('manage_subscriptions')) {
            abort(403);
        }

        $plans = $this->planService->listPlans(
            $request->only(['search', 'is_active']),
            $request->get('per_page', 15)
        );

        if ($request->wantsJson()) {
            return $this->paginatedResponse($plans, 'plans', 'تم استرجاع الخطط بنجاح');
        }

        return view('spa.plans.index', compact('plans'));
    }

    /**
     * Store a newly created plan.
     */
    public function store(CreatePlanRequest $request): JsonResponse
    {
        Gate::authorize('manage_plans');

        try {
            $plan = $this->planService->createPlan($request->validated());

            return $this->successResponse(
                ['plan' => $plan],
                'تم إضافة الخطة بنجاح'
            );
        } catch (\Exception $e) {
            Log::error('Error creating plan: ' . $e->getMessage());

            return $this->errorResponse(
                'حدث خطأ أثناء إضافة الخطة',
                500,
                ['error' => config('app.debug') ? $e->getMessage() : null]
            );
        }
    }

    /**
     * Show the form for editing the specified plan.
     */
    public function edit(Plan $plan): View|JsonResponse
    {
        Gate::authorize('manage_plans');

        if (request()->wantsJson()) {
            return $this->successResponse($plan);
        }

        return view('spa.plans.edit', compact('plan'));
    }

    /**
     * Update the specified plan.
     */
    public function update(UpdatePlanRequest $request, Plan $plan): JsonResponse
    {
        Gate::authorize('manage_plans');

        try {
            $updatedPlan = $this->planService->updatePlan($plan, $request->validated());

            return $this->successResponse(
                ['plan' => $updatedPlan],
                'تم تحديث الخطة بنجاح'
            );
        } catch (\Exception $e) {
            Log::error('Error updating plan: ' . $e->getMessage());

            return $this->errorResponse(
                'حدث خطأ أثناء تحديث الخطة',
                500,
                ['error' => config('app.debug') ? $e->getMessage() : null]
            );
        }
    }

    /**
     * Remove the specified plan.
     */
    public function destroy(Plan $plan): JsonResponse|RedirectResponse
    {
        Gate::authorize('manage_plans');

        try {
            $this->planService->deletePlan($plan);

            if (request()->wantsJson()) {
                return $this->successResponse(null, 'تم حذف الخطة بنجاح');
            }

            return redirect()->back()->with('success', 'تم حذف الخطة بنجاح');
        } catch (\Exception $e) {
            Log::error('Error deleting plan: ' . $e->getMessage());

            return $this->handleError($e, request(), 'حدث خطأ أثناء حذف الخطة');
        }
    }

    /**
     * Handle errors consistently.
     */
    private function handleError(\Exception $e, Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson()) {
            return $this->errorResponse(
                $message,
                500,
                ['error' => config('app.debug') ? $e->getMessage() : null]
            );
        }

        return back()->with('error', $message);
    }
}
