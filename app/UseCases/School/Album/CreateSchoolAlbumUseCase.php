<?php

namespace App\UseCases\School\Album;

use App\Models\Album;
use App\Models\School;
use App\Models\StorageLibrary;
use Exception;
use Illuminate\Support\Facades\DB;

class CreateSchoolAlbumUseCase
{
    /**
     * Execute the use case.
     */
    public function execute(School $school, array $data): Album
    {
        $user = $school->user;
        $activeSubscription = $user->activeSubscription;
        $plan = $activeSubscription?->plan;

        if (!$plan) {
            throw new Exception('المدرسة ليس لديها اشتراك نشط');
        }

        // Discover Storage Library
        $storageLibrary = StorageLibrary::where('school_id', $school->school_id)->first();
        if (!$storageLibrary) {
             throw new Exception('لم يتم العثور على مكتبة تخزين للمدرسة. يرجى تحديث الصفحة أو التواصل مع الإدارة إذا استمرت المشكلة.');
        }

        return DB::transaction(function () use ($school, $data, $storageLibrary) {
            $album = $school->albums()->create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'storage_library_id' => $storageLibrary->storage_library_id,
                'is_visible' => $data['is_visible'] ?? true,
                'settings' => $data['settings'] ?? [],
            ]);

            return $album;
        });
    }
}
