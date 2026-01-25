<?php

namespace App\Domain\Finance\Repositories\Eloquent;

use App\Domain\Shared\Repositories\Eloquent\BaseRepository;
use App\Domain\Finance\Repositories\Contracts\SubscriptionRepositoryInterface;
use App\Domain\Finance\Models\\Subscription;

class SubscriptionRepository extends BaseRepository implements SubscriptionRepositoryInterface
{
    public function __construct(Subscription \Subscription)
    {
        parent::__construct(\Subscription);
    }
}
