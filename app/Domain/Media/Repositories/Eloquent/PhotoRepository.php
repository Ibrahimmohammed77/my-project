<?php

namespace App\Domain\Media\Repositories\Eloquent;

use App\Domain\Shared\Repositories\Eloquent\BaseRepository;
use App\Domain\Media\Repositories\Contracts\PhotoRepositoryInterface;
use App\Domain\Media\Models\Photo;

class PhotoRepository extends BaseRepository implements PhotoRepositoryInterface
{
    public function __construct(Photo $photo)
    {
        parent::__construct($photo);
    }
}
