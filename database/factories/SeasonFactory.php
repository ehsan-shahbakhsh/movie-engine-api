<?php

namespace Database\Factories;

use App\Models\Season;
use App\Models\Series;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Season>
 */
class SeasonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $releaseDate = fake()->optional()->dateTimeBetween('1900-01-01', '2026-12-31');

        return [
            'series_id' => Series::factory(),

            'season_number' => fake()->numberBetween(1, 15),
            'title' => fake()->optional()->sentence(2),

            'release_date' => $releaseDate?->format('Y-m-d'),
            'end_date' => fake()->optional(70)->dateTimeBetween($releaseDate, '2026-12-31')?->format('Y-m-d'),
        ];
    }
}
