<?php

namespace App\Domain\Core\Repositories\Eloquent;

use App\Domain\Shared\Repositories\Eloquent\BaseRepository;
use App\Domain\Core\Repositories\Contracts\StudioRepositoryInterface;
use App\Domain\Core\Models\Studio;

class StudioRepository extends BaseRepository implements StudioRepositoryInterface
{
    public function __construct(Studio $studio)
    {
        parent::__construct($studio);
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
        return $this->model->where('account_id', $accountId)->get();
    }

    public function findWithRelations(int $id, array $relations = [])
    {
        $query = $this->model->newQuery();
        
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        return $query->find($id);
    }

    public function search(string $searchTerm)
    {
        return $this->model
            ->where('name', 'LIKE', "%{$searchTerm}%")
            ->orWhere('email', 'LIKE', "%{$searchTerm}%")
            ->orWhere('phone', 'LIKE', "%{$searchTerm}%")
            ->orWhere('city', 'LIKE', "%{$searchTerm}%")
            ->orWhere('country', 'LIKE', "%{$searchTerm}%")
            ->get();
    }

    public function updateStatus(int $id, string $statusCode): bool
    {
        $studio = $this->find($id);
        if (!$studio) {
            return false;
        }

        $status = \App\Domain\Shared\Models\LookupValue::where('code', $statusCode)->first();
        if (!$status) {
            return false;
        }

        return $studio->update(['studio_status_id' => $status->lookup_value_id]);
    }
}
