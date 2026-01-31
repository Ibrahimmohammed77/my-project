<?php

namespace App\Services\Admin;

use App\Repositories\Contracts\PlanRepositoryInterface;
use App\Models\Plan;
use Illuminate\Pagination\LengthAwarePaginator;

class PlanService
{
    public function __construct(
        protected PlanRepositoryInterface $planRepository
    ) {}

    public function listPlans(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->planRepository->listByAdmin($filters, $perPage);
    }

    public function createPlan(array $data): Plan
    {
        return $this->planRepository->store($data);
    }

    public function updatePlan(Plan $plan, array $data): Plan
    {
        return $this->planRepository->update($plan, $data);
    }

    public function deletePlan(Plan $plan): bool
    {
        return $this->planRepository->delete($plan);
    }
}
