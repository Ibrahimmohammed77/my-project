<?php

namespace Database\Factories;

use App\Models\StorageLibrary;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StorageLibraryFactory extends Factory
{
    protected $model = StorageLibrary::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->word() . ' Library',
            'storage_limit' => 1024 * 1024, // 1GB
        ];
    }
}
