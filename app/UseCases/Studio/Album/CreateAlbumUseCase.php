<?php

namespace App\UseCases\Studio\Album;

use App\Models\Album;
use App\Models\Studio;
use Exception;
use Illuminate\Support\Facades\DB;

class CreateAlbumUseCase
{
    /**
     * Execute the use case.
     */
    public function execute(Studio $studio, array $data): Album
    {
        $user = $studio->user;
        $activeSubscription = $user->activeSubscription;
        $plan = $activeSubscription?->plan;

        if (!$plan) {
            throw new Exception('الاستوديو ليس لديه اشتراك نشط');
        }

        return DB::transaction(function () use ($studio, $data) {
            $album = $studio->albums()->create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'storage_library_id' => $data['storage_library_id'] ?? null,
                'is_visible' => $data['is_visible'] ?? true,
                'settings' => $data['settings'] ?? [],
            ]);

            return $album;
        });
    }
}
