<?php

namespace App\Domain\Shared\Repositories\Eloquent;

use App\Domain\Shared\Repositories\Eloquent\BaseRepository;
use App\Domain\Shared\Repositories\Contracts\LookupValueRepositoryInterface;
use App\Domain\Shared\Models\\LookupValue;

class LookupValueRepository extends BaseRepository implements LookupValueRepositoryInterface
{
    public function __construct(LookupValue \LookupValue)
    {
        parent::__construct(\LookupValue);
    }
}
