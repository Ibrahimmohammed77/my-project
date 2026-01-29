<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'plan_id' => Plan::factory(),
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'renewal_date' => now()->addMonth()->subDays(7),
            'auto_renew' => true,
        ];
    }
}
