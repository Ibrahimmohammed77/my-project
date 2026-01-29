<?php

namespace Database\Factories;

use App\Models\StorageLibrary;
use App\Models\Studio;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StorageLibraryFactory extends Factory
{
    protected $model = StorageLibrary::class;

    public function definition(): array
    {
        return [
            'studio_id' => Studio::factory(),
            'user_id' => User::factory(),
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'storage_limit' => 1024 * 1024 * 50, // 50MB
        ];
    }
}
