<?php

namespace Database\Factories;

use App\Models\Quality;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quality>
 */
class QualityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                '360p',
                '480p',
                '720p',
                '1080p',
                '1440p',
                '2160p',
                '4320p',
            ]),
        ];
    }
}
