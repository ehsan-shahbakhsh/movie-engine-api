<?php

namespace Database\Factories;

use App\Models\Episode;
use App\Models\Season;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Episode>
 */
class EpisodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'season_id' => Season::factory(),

            'episode_number' => fake()->numberBetween(1, 20),
            'title' => fake()->optional()->sentence(3),
            'synopsis' => fake()->optional()->paragraph(),
            'air_date' => fake()->date(),
            'duration_minutes' => fake()->optional(90)->numberBetween(30, 100),
        ];
    }
}
