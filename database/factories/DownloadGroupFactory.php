<?php

namespace Database\Factories;

use App\Models\DownloadGroup;
use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DownloadGroup>
 */
class DownloadGroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'downloadable_type' => Movie::class,
            'downloadable_id' => Movie::factory(),

            'title' => fake()->sentence(2),
            'sort_order' => fake()->numberBetween(1, 10),
            'is_active' => fake()->boolean(),
        ];
    }
}
