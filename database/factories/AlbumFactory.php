<?php

namespace Database\Factories;

use App\Models\Album;
use App\Models\Studio;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlbumFactory extends Factory
{
    protected $model = Album::class;

    public function definition(): array
    {
        return [
            'owner_type' => Studio::class,
            'owner_id' => Studio::factory(),
            'storage_library_id' => \App\Models\StorageLibrary::factory(),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'is_visible' => true,
            'is_default' => false,
            'settings' => [],
        ];
    }
}
