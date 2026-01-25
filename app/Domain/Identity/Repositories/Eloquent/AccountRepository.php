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
}
