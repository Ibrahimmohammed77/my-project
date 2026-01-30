<?php

namespace Database\Factories;

use App\Models\LookupMaster;
use Illuminate\Database\Eloquent\Factories\Factory;

class LookupMasterFactory extends Factory
{
    protected $model = LookupMaster::class;

    public function definition()
    {
        return [
            'code' => $this->faker->unique()->word(),
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
        ];
    }
}
