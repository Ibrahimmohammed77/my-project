<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word() . ' Plan',
            'storage_limit' => 1024 * 1024 * 10, // 10GB
            'price_monthly' => $this->faker->randomFloat(2, 10, 50),
            'price_yearly' => $this->faker->randomFloat(2, 100, 500),
            'max_albums' => 10,
            'max_cards' => 100,
            'max_users' => 5,
            'max_storage_libraries' => 1,
            'is_active' => true,
        ];
    }
}
