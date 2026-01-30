<?php

namespace Database\Factories;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolFactory extends Factory
{
    protected $model = School::class;

    public function definition()
    {
        return [
            'user_id' => User::factory()->schoolOwner(),
            'description' => $this->faker->sentence(),
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'settings' => [],
        ];
    }
}
