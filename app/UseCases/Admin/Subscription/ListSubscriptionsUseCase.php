<?php

namespace App\UseCases\Admin\Subscription;

use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ListSubscriptionsUseCase
{
    protected $subscriptionRepository;

    public function __construct(SubscriptionRepositoryInterface $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * Execute the use case.
     */
    public function execute(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->subscriptionRepository->listByAdmin($filters, $perPage);
    }
}
