<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreatePlanRequest;
use App\Http\Requests\Admin\UpdatePlanRequest;
use App\Models\Plan;
use App\UseCases\Admin\Plan\ListPlansUseCase;
use App\UseCases\Admin\Plan\CreatePlanUseCase;
use App\UseCases\Admin\Plan\UpdatePlanUseCase;
use App\UseCases\Admin\Plan\DeletePlanUseCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PlanController extends Controller
{
    protected $listPlansUseCase;
    protected $createPlanUseCase;
    protected $updatePlanUseCase;
    protected $deletePlanUseCase;

    public function __construct(
        ListPlansUseCase $listPlansUseCase,
        CreatePlanUseCase $createPlanUseCase,
        UpdatePlanUseCase $updatePlanUseCase,
        DeletePlanUseCase $deletePlanUseCase
    ) {
        $this->listPlansUseCase = $listPlansUseCase;
        $this->createPlanUseCase = $createPlanUseCase;
        $this->updatePlanUseCase = $updatePlanUseCase;
        $this->deletePlanUseCase = $deletePlanUseCase;
    }

    public function index(Request $request)
    {
        Gate::authorize('manage_plans');

        $plans = $this->listPlansUseCase->execute(
            $request->only(['search', 'is_active']),
            $request->get('per_page', 15)
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'plans' => $plans
                ]
            ]);
        }

        return view('spa.plans.index', compact('plans'));
    }

    public function store(CreatePlanRequest $request)
    {
        Gate::authorize('manage_plans');

        $this->createPlanUseCase->execute($request->validated());

        return redirect()->back()->with('success', 'تم إضافة الخطة بنجاح');
    }

    public function edit(Plan $plan)
    {
        Gate::authorize('manage_plans');

        if (request()->wantsJson()) {
            return response()->json($plan);
        }

        return view('spa.plans.edit', compact('plan'));
    }

    public function update(UpdatePlanRequest $request, Plan $plan)
    {
        Gate::authorize('manage_plans');

        $this->updatePlanUseCase->execute($plan, $request->validated());

        return redirect()->back()->with('success', 'تم تحديث الخطة بنجاح');
    }

    public function destroy(Plan $plan)
    {
        Gate::authorize('manage_plans');

        $this->deletePlanUseCase->execute($plan);

        return redirect()->back()->with('success', 'تم حذف الخطة بنجاح');
    }
}
