<?php

namespace App\Repositories\Contracts;

use App\Models\Plan;
use Illuminate\Pagination\LengthAwarePaginator;

interface PlanRepositoryInterface
{
    public function listByAdmin(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function store(array $data): Plan;
    public function update(Plan $plan, array $data): Plan;
    public function delete(Plan $plan): bool;
    public function find(int $id): ?Plan;
}
