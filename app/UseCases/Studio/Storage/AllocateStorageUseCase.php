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
        $studioPlan = $studioUser->activeSubscription()?->plan;
        
        if (!$studioPlan) {
            throw new Exception('الاستوديو ليس لديه اشتراك نشط');
        }

        // Limit check for storage libraries count
        $currentLibrariesCount = $studio->storageLibraries()->count();
        if ($currentLibrariesCount >= $studioPlan->max_storage_libraries) {
            throw new Exception('تم الوصول للحد الأقصى لمكتبات التخزين المسموح بها');
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
