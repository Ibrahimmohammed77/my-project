<?php

namespace App\Repositories;

use App\Models\LookupMaster;
use App\Models\LookupValue;
use App\Repositories\Contracts\LookupRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class LookupRepository implements LookupRepositoryInterface
{
    public function listMasters(): Collection
    {
        return LookupMaster::with(['values' => function ($q) {
            $q->orderBy('sort_order');
        }])->get();
    }

    public function findMaster(string $code): ?LookupMaster
    {
        return LookupMaster::where('code', $code)->first();
    }

    public function storeValue(array $data): LookupValue
    {
        return DB::transaction(function () use ($data) {
            return LookupValue::create($data);
        });
    }

    public function updateValue(LookupValue $value, array $data): LookupValue
    {
        return DB::transaction(function () use ($value, $data) {
            $value->update($data);
            return $value->refresh();
        });
    }

    public function deleteValue(LookupValue $value): bool
    {
        return $value->delete();
    }

    public function findValue(int $id): ?LookupValue
    {
        return LookupValue::find($id);
    }
}
