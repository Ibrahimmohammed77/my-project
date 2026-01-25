<?php

namespace App\Domain\Media\Repositories\Eloquent;

use App\Domain\Shared\Repositories\Eloquent\BaseRepository;
use App\Domain\Media\Repositories\Contracts\AlbumRepositoryInterface;
use App\Domain\Media\Models\\Album;

class AlbumRepository extends BaseRepository implements AlbumRepositoryInterface
{
    public function __construct(Album \Album)
    {
        parent::__construct(\Album);
    }
}
