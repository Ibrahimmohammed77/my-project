<?php

namespace App\UseCases\Admin\Plan;

use App\Repositories\Contracts\PlanRepositoryInterface;
use App\Models\Plan;

class CreatePlanUseCase
{
    protected $planRepository;

    public function __construct(PlanRepositoryInterface $planRepository)
    {
        $this->planRepository = $planRepository;
    }

    public function execute(array $data): Plan
    {
        return $this->planRepository->store($data);
    }
}
