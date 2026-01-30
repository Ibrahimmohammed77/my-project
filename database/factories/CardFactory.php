<?php

namespace Database\Factories;

use App\Models\Card;
use App\Models\Studio;
use App\Models\LookupValue;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CardFactory extends Factory
{
    protected $model = Card::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'card_uuid' => (string) Str::uuid(),
            'card_number' => (string) $this->faker->unique()->numberBetween(100000000000, 999999999999),
            'owner_type' => Studio::class,
            'owner_id' => Studio::factory(),
            'card_type_id' => LookupValue::factory(),
            'card_status_id' => LookupValue::factory(),
            'activation_date' => now(),
            'expiry_date' => now()->addYear(),
        ];
    }
}
