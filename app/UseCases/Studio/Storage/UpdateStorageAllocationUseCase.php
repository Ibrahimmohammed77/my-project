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
        $user = $studio->user;
        $plan = $user->activeSubscription?->plan;

        if (!$plan) {
            throw new Exception('الاستوديو ليس لديه اشتراك نشط');
        }

        // Check if new limit is less than currently used storage
        if (isset($data['storage_limit'])) {
            $newLimit = (int) $data['storage_limit'];
            
            if ($newLimit < $library->used_storage) {
                throw new Exception('لا يمكن تقليل المساحة المخصصة لأقل من المساحة المستخدمة حالياً');
            }

            // Check if total allocated storage exceeds plan limit
            $otherAllocated = $studio->storageLibraries()
                ->where('storage_library_id', '!=', $libraryId)
                ->sum('storage_limit');

            if (($otherAllocated + $newLimit) > $plan->storage_limit) {
                throw new Exception('المساحة المطلوبة تتجاوز الحد المسموح به في خطتك (' . $plan->storage_limit . ' بايت)');
            }
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
