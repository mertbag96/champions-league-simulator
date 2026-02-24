<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Fixture>
 */
class FixtureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'home_team_id' => \App\Models\Team::factory(),
            'away_team_id' => \App\Models\Team::factory(),
            'week' => $this->faker->numberBetween(1, 6),
            'home_score' => 0,
            'away_score' => 0,
            'played' => false,
        ];
    }
}
