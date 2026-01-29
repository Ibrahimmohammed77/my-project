<?php

namespace App\UseCases\Admin\Lookup;

use App\Repositories\Contracts\LookupRepositoryInterface;
use App\Models\LookupValue;

class ManageLookupValueUseCase
{
    protected $lookupRepository;

    public function __construct(LookupRepositoryInterface $lookupRepository)
    {
        $this->lookupRepository = $lookupRepository;
    }

    public function create(array $data): LookupValue
    {
        return $this->lookupRepository->storeValue($data);
    }

    public function update(LookupValue $value, array $data): LookupValue
    {
        return $this->lookupRepository->updateValue($value, $data);
    }

    public function delete(LookupValue $value): bool
    {
        return $this->lookupRepository->deleteValue($value);
    }
}
