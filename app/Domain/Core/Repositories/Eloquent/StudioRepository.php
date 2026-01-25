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
}
