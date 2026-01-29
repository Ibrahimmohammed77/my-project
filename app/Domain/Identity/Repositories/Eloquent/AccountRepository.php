<?php

namespace App\Domain\Identity\Repositories\Eloquent;

use App\Domain\Shared\Repositories\Eloquent\BaseRepository;
use App\Domain\Identity\Repositories\Contracts\AccountRepositoryInterface;
use App\Domain\Identity\Models\Account;

class AccountRepository extends BaseRepository implements AccountRepositoryInterface
{
    public function __construct(Account $account)
    {
        parent::__construct($account);
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
            ->where('username', 'LIKE', "%{$searchTerm}%")
            ->orWhere('email', 'LIKE', "%{$searchTerm}%")
            ->orWhere('full_name', 'LIKE', "%{$searchTerm}%")
            ->orWhere('phone', 'LIKE', "%{$searchTerm}%")
            ->get();
    }

    public function updateStatus(int $id, string $statusCode): bool
    {
        $account = $this->find($id);
        if (!$account) {
            return false;
        }

        $status = \App\Domain\Shared\Models\LookupValue::where('code', $statusCode)->first();
        if (!$status) {
            return false;
        }

        return $account->update(['account_status_id' => $status->lookup_value_id]);
    }

    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function findByUsername(string $username)
    {
        return $this->model->where('username', $username)->first();
    }
}
