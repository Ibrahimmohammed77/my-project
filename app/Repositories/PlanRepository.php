<?php

namespace App\Repositories;

use App\Models\Plan;
use App\Repositories\Contracts\PlanRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PlanRepository implements PlanRepositoryInterface
{
    protected $model;

    public function __construct(Plan $model)
    {
        $this->model = $model;
    }

    public function listByAdmin(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with('billingCycle');

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function store(array $data): Plan
    {
        return DB::transaction(function () use ($data) {
            return $this->model->create($data);
        });
    }

    public function update(Plan $plan, array $data): Plan
    {
        return DB::transaction(function () use ($plan, $data) {
            $plan->update($data);
            return $plan->refresh();
        });
    }

    public function delete(Plan $plan): bool
    {
        return $plan->delete();
    }

    public function find(int $id): ?Plan
    {
        return $this->model->find($id);
    }
}
