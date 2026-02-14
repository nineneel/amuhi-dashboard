<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubscriptionPlan>
 */
class SubscriptionPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => Str::title(fake()->unique()->words(3, true)),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 100_000, 1_000_000),
            'duration_days' => fake()->randomElement([30, 365]),
            'features' => [
                fake()->sentence(3),
                fake()->sentence(3),
                fake()->sentence(3),
            ],
            'is_active' => true,
        ];
    }
}
