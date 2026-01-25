<?php

namespace App\Domain\Media\Repositories\Eloquent;

use App\Domain\Shared\Repositories\Eloquent\BaseRepository;
use App\Domain\Media\Repositories\Contracts\StorageAccountRepositoryInterface;
use App\Domain\Media\Models\StorageAccount;

class StorageAccountRepository extends BaseRepository implements StorageAccountRepositoryInterface
{
    public function __construct(StorageAccount $storageAccount)
    {
        parent::__construct($storageAccount);
    }
}
