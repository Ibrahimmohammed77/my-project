<?php

namespace App\Domain\Core\Repositories\Eloquent;

use App\Domain\Shared\Repositories\Eloquent\BaseRepository;
use App\Domain\Core\Repositories\Contracts\OfficeRepositoryInterface;
use App\Domain\Core\Models\Office;

class OfficeRepository extends BaseRepository implements OfficeRepositoryInterface
{
    public function __construct(Office $office)
    {
        parent::__construct($office);
    }
}
