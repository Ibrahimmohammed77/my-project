<?php

namespace App\UseCases\Admin\Plan;

use App\Repositories\Contracts\PlanRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ListPlansUseCase
{
    protected $planRepository;

    public function __construct(PlanRepositoryInterface $planRepository)
    {
        $this->planRepository = $planRepository;
    }

    public function execute(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->planRepository->listByAdmin($filters, $perPage);
    }
}
