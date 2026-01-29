<?php

namespace Database\Factories;

use App\Models\Plan;
use App\Models\LookupValue;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'storage_limit' => 1024 * 1024 * 100, // 100MB
            'price_monthly' => 10.00,
            'price_yearly' => 100.00,
            'max_albums' => 10,
            'max_cards' => 50,
            'max_users' => 1,
            'max_storage_libraries' => 5,
            'features' => [],
            'is_active' => true,
        ];
    }
}
