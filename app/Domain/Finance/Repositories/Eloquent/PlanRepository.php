<?php

namespace App\Domain\Finance\Repositories\Eloquent;

use App\Domain\Shared\Repositories\Eloquent\BaseRepository;
use App\Domain\Finance\Repositories\Contracts\PlanRepositoryInterface;
use App\Domain\Finance\Models\\Plan;

class PlanRepository extends BaseRepository implements PlanRepositoryInterface
{
    public function __construct(Plan \Plan)
    {
        parent::__construct(\Plan);
    }
}
