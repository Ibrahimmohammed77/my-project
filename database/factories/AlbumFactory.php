<?php

namespace Database\Factories;

use App\Models\Album;
use App\Models\School;
use App\Models\StorageLibrary;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlbumFactory extends Factory
{
    protected $model = Album::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word() . ' Album',
            'owner_type' => School::class,
            'owner_id' => School::factory(),
            'storage_library_id' => StorageLibrary::factory(),
            'is_visible' => true,
            'settings' => [],
        ];
    }

    public function visible(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_visible' => true,
        ]);
    }
}
