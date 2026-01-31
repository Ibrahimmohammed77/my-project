<?php

namespace App\Repositories;

use App\Models\Subscription;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SubscriptionRepository implements SubscriptionRepositoryInterface
{
    protected $model;

    public function __construct(Subscription $model)
    {
        $this->model = $model;
    }

    /**
     * List subscriptions with filters.
     */
    public function listByAdmin(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['user', 'plan', 'status']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('username', 'LIKE', "%{$search}%");
            });
        }

        if (!empty($filters['plan_id'])) {
            $query->where('plan_id', $filters['plan_id']);
        }

        if (!empty($filters['status_id'])) {
            $query->where('subscription_status_id', $filters['status_id']);
        }

        $query->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
    }

    /**
     * Find a subscription by ID.
     */
    public function find(int $id): ?Subscription
    {
        return $this->model->with(['user', 'plan', 'status'])->find($id);
    }

    /**
     * Create or update a subscription.
     */
    public function store(array $data): Subscription
    {
        return DB::transaction(function () use ($data) {
            return $this->model->updateOrCreate(
                ['user_id' => $data['user_id']],
                $data
            );
        });
    }

    /**
     * Update a subscription.
     */
    public function update(Subscription $subscription, array $data): Subscription
    {
        return DB::transaction(function () use ($subscription, $data) {
            $subscription->update($data);
            return $subscription->refresh();
        });
    }

    /**
     * Delete a subscription.
     */
    public function delete(Subscription $subscription): bool
    {
        return $subscription->delete();
    }

    /**
     * Find subscriptions by user.
     */
    public function findByUser(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->where('user_id', $userId)
            ->with(['plan', 'status'])
            ->orderBy('created_at', 'desc');

        if (!empty($filters['status_id'])) {
            $query->where('subscription_status_id', $filters['status_id']);
        }

        return $query->paginate($perPage);
    }

    /**
     * Find active subscription for user.
     */
    public function findActiveByUser(int $userId): ?Subscription
    {
        return $this->model->where('user_id', $userId)
            ->where('end_date', '>=', now())
            ->whereHas('status', function($q) {
                $q->where('code', 'ACTIVE');
            })
            ->first();
    }
}