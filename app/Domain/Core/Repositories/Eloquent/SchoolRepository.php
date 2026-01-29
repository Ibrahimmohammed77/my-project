<?php

namespace App\Domain\Core\Repositories\Eloquent;

use App\Domain\Shared\Repositories\Eloquent\BaseRepository;
use App\Domain\Core\Repositories\Contracts\SchoolRepositoryInterface;
use App\Domain\Core\Models\School;

class SchoolRepository extends BaseRepository implements SchoolRepositoryInterface
{
    public function __construct(School $school)
    {
        parent::__construct($school);
    }

    public function findActive()
    {
        return $this->model->active()->get();
    }

    public function findByStatus(string $statusCode)
    {
        return $this->model->status($statusCode)->get();
    }

    public function findByType(string $typeCode)
    {
        return $this->model->type($typeCode)->get();
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
        $school = $this->find($id);
        if (!$school) {
            return false;
        }

        $status = \App\Domain\Shared\Models\LookupValue::where('code', $statusCode)->first();
        if (!$status) {
            return false;
        }

        return $school->update(['school_status_id' => $status->lookup_value_id]);
    }
}
