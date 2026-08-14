<?php

namespace Database\Factories;

use App\Enums\VideoType;
use App\Models\Movie;
use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Video>
 */
class VideoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'videoable_type' => Movie::class,
            'videoable_id' => Movie::factory(),

            'name' => fake()->sentence(3),
            'type' => fake()->randomElement(VideoType::cases()),

            'is_official' => fake()->boolean(80),
            'is_active' => fake()->boolean(80),
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }
}
