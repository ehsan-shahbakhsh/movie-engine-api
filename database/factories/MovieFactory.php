<?php

namespace Database\Factories;

use App\Enums\MovieStatus;
use App\Models\AgeRating;
use App\Models\Language;
use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Movie>
 */
class MovieFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(3);
        $originalTitle = fake()->boolean() ? fake()->sentence(3) : null;
        $slug = Str::slug($originalTitle ?? $title) . '-' . fake()->unique()->numberBetween(1, 999999);

        $releaseDate = fake()->optional()->dateTimeBetween('1900-01-01', '2026-12-31');

        return [
            'title' => $title,
            'original_title' => $originalTitle,
            'slug' => $slug,

            'synopsis' => fake()->boolean(80) ? fake()->paragraph() : null,
            'release_year' => $releaseDate?->year ?? fake()->numberBetween(1900, 2026),
            'release_date' => $releaseDate?->format('Y-m-d'),
            'duration_minutes' => fake()->boolean(90) ? fake()->numberBetween(30, 300) : null,

            'age_rating_id' => AgeRating::factory(),
            'original_language_id' => Language::factory(),

            'status' => fake()->randomElement(MovieStatus::cases()),
        ];
    }
}
