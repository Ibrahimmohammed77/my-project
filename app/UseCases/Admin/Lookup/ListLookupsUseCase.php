<?php

namespace App\UseCases\Admin\Lookup;

use App\Repositories\Contracts\LookupRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ListLookupsUseCase
{
    protected $lookupRepository;

    public function __construct(LookupRepositoryInterface $lookupRepository)
    {
        $this->lookupRepository = $lookupRepository;
    }

    public function execute(): Collection
    {
        return $this->lookupRepository->listMasters();
    }
}
