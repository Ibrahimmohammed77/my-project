<?php

namespace App\Domain\Finance\Repositories\Eloquent;

use App\Domain\Shared\Repositories\Eloquent\BaseRepository;
use App\Domain\Finance\Repositories\Contracts\CommissionRepositoryInterface;
use App\Domain\Finance\Models\Commission;

class CommissionRepository extends BaseRepository implements CommissionRepositoryInterface
{
    public function __construct(Commission $commission)
    {
        parent::__construct($commission);
    }
}
