<?php

namespace App\Domain\Core\Repositories\Eloquent;

use App\Domain\Shared\Repositories\Eloquent\BaseRepository;
use App\Domain\Core\Repositories\Contracts\SchoolRepositoryInterface;
use App\Domain\Core\Models\\School;

class SchoolRepository extends BaseRepository implements SchoolRepositoryInterface
{
    public function __construct(School \School)
    {
        parent::__construct(\School);
    }
}
