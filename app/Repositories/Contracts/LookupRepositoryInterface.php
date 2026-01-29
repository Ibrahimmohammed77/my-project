<?php

namespace App\Repositories\Contracts;

use App\Models\LookupMaster;
use App\Models\LookupValue;
use Illuminate\Database\Eloquent\Collection;

interface LookupRepositoryInterface
{
    public function listMasters(): Collection;
    public function findMaster(string $code): ?LookupMaster;
    public function storeValue(array $data): LookupValue;
    public function updateValue(LookupValue $value, array $data): LookupValue;
    public function deleteValue(LookupValue $value): bool;
    public function findValue(int $id): ?LookupValue;
}
