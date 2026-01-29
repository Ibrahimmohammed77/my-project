<?php

namespace App\Domain\Core\Repositories\Eloquent;

use App\Domain\Shared\Repositories\Eloquent\BaseRepository;
use App\Domain\Core\Repositories\Contracts\SubscriberRepositoryInterface;
use App\Domain\Core\Models\Subscriber;

class SubscriberRepository extends BaseRepository implements SubscriberRepositoryInterface
{
    public function __construct(Subscriber $subscriber)
    {
        parent::__construct($subscriber);
    }

    public function findActive()
    {
        return $this->model->active()->get();
    }

    public function findByStatus(string $statusCode)
    {
        return $this->model->status($statusCode)->get();
    }

    public function findByAccount(int $accountId)
    {
        return $this->model->where('account_id', $accountId)->first();
    }

    public function findWithRelations(int $id, array $relations = [])
    {
        $query = $this->model->newQuery();
        
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        return $query->find($id);
    }

    public function updateStatus(int $id, string $statusCode): bool
    {
        $subscriber = $this->find($id);
        if (!$subscriber) {
            return false;
        }

        $status = \App\Domain\Shared\Models\LookupValue::where('code', $statusCode)->first();
        if (!$status) {
            return false;
        }

        return $subscriber->update(['subscriber_status_id' => $status->lookup_value_id]);
    }
}
