<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Team ' . $this->faker->unique()->randomNumber(),
            'power' => $this->faker->numberBetween(50, 100),
            'played' => 0,
            'points' => 0,
            'goals_for' => 0,
            'goals_against' => 0,
            'active' => true,
        ];
    }
}
