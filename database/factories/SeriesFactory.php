<?php

namespace Database\Factories;

use App\Enums\SeriesProductionStatus;
use App\Enums\SeriesPublishStatus;
use App\Models\AgeRating;
use App\Models\Language;
use App\Models\Series;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Series>
 */
class SeriesFactory extends Factory
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

        $productionStatus = fake()->randomElement(SeriesProductionStatus::cases());

        $endDate = match($productionStatus) {
            SeriesProductionStatus::Ongoing => null,
            default => fake()->optional(70)->dateTimeBetween($releaseDate, '2026-12-31')?->format('Y-m-d')
        };

        return [
            'title' => $title,
            'original_title' => $originalTitle,
            'slug' => $slug,

            'synopsis' => fake()->boolean(80) ? fake()->paragraph() : null,
            'release_year' => $releaseDate?->year ?? fake()->numberBetween(1900, 2026),
            'release_date' => $releaseDate?->format('Y-m-d'),
            'end_date' => $endDate,

            'age_rating_id' => AgeRating::factory(),
            'original_language_id' => Language::factory(),

            'publish_status' => fake()->randomElement(SeriesPublishStatus::cases()),
            'production_status' => $productionStatus,
        ];
    }

    public function published(): static
    {
        return $this->state(fn(array $attributes) => [
            'publish_status' => SeriesPublishStatus::Published,
        ]);
    }
}
