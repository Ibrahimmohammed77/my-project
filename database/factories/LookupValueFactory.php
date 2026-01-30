<?php

namespace Database\Factories;

use App\Models\LookupValue;
use App\Models\LookupMaster;
use Illuminate\Database\Eloquent\Factories\Factory;

class LookupValueFactory extends Factory
{
    protected $model = LookupValue::class;

    public function definition()
    {
        return [
            'lookup_master_id' => LookupMaster::factory(),
            'code' => $this->faker->unique()->word(),
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
