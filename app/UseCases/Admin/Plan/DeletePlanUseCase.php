<?php

namespace App\UseCases\Admin\Plan;

use App\Repositories\Contracts\PlanRepositoryInterface;
use App\Models\Plan;

class DeletePlanUseCase
{
    protected $planRepository;

    public function __construct(PlanRepositoryInterface $planRepository)
    {
        $this->planRepository = $planRepository;
    }

    public function execute(Plan $plan): bool
    {
        return $this->planRepository->delete($plan);
    }
}
