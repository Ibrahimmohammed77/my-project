<?php

namespace App\UseCases\Studio\Storage;

use App\Models\StorageLibrary;
use App\Models\Studio;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class AllocateStorageUseCase
{
    /**
     * Execute the use case.
     */
    public function execute(Studio $studio, array $data): StorageLibrary
    {
        // Check studio storage capacity
        $studioUser = $studio->user;
        $studioPlan = $studioUser->activeSubscription?->plan;
        
        if (!$studioPlan) {
            throw new Exception('الاستوديو ليس لديه اشتراك نشط');
        }

        // Ensure storage allocation doesn't exceed total plan storage
        $currentAllocated = $studio->storageLibraries()->sum('storage_limit');
        $requestedLimit = (int) $data['storage_limit'];
        
        if (($currentAllocated + $requestedLimit) > $studioPlan->storage_limit) {
            throw new Exception('المساحة المطلوبة تتجاوز الحد المسموح به في خطتك (' . $studioPlan->storage_limit . ' بايت)');
        }

        return DB::transaction(function () use ($studio, $data) {
            $library = StorageLibrary::create([
                'studio_id' => $studio->studio_id,
                'user_id' => $data['subscriber_id'],
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'storage_limit' => $data['storage_limit'], // In bytes
            ]);

            return $library;
        });
    }
}
