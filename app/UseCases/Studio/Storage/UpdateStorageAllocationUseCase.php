<?php

namespace App\UseCases\Studio\Storage;

use App\Models\StorageLibrary;
use App\Models\Studio;
use Exception;
use Illuminate\Support\Facades\DB;

class UpdateStorageAllocationUseCase
{
    /**
     * Execute the use case.
     */
    public function execute(Studio $studio, int $libraryId, array $data): StorageLibrary
    {
        $library = $studio->storageLibraries()->findOrFail($libraryId);

        // Check if new limit is less than currently used storage
        if (isset($data['storage_limit']) && $data['storage_limit'] < $library->used_storage) {
            throw new Exception('لا يمكن تقليل المساحة المخصصة لأقل من المساحة المستخدمة حالياً');
        }

        return DB::transaction(function () use ($library, $data) {
            $library->update([
                'name' => $data['name'] ?? $library->name,
                'description' => $data['description'] ?? $library->description,
                'storage_limit' => $data['storage_limit'] ?? $library->storage_limit,
            ]);

            return $library;
        });
    }
}
