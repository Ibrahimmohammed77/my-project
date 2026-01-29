<?php

namespace App\UseCases\Admin\Plan;

use App\Repositories\Contracts\PlanRepositoryInterface;
use App\Models\Plan;

class UpdatePlanUseCase
{
    protected $planRepository;

    public function __construct(PlanRepositoryInterface $planRepository)
    {
        $this->planRepository = $planRepository;
    }

    public function execute(Plan $plan, array $data): Plan
    {
        return $this->planRepository->update($plan, $data);
    }
}
