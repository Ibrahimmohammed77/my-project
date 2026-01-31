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
            'description' => $this->faker->sentence(),
            'storage_limit' => 1024 * 1024 * 10, // 10GB
            'price_monthly' => $this->faker->randomFloat(2, 10, 50),
            'price_yearly' => $this->faker->randomFloat(2, 100, 500),
            'features' => ['High quality photos', 'Infinite albums'],
            'is_active' => true,
        ];
    }
}
