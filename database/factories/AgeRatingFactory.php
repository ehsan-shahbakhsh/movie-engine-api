<?php

namespace Database\Factories;

use App\Models\AgeRating;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AgeRating>
 */
class AgeRatingFactory extends Factory
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
                'G',
                'PG',
                'PG-13',
                'TV-14',
                'R',
                'TV-MA',
                'NC-17',
                'NR',
            ]),
        ];
    }
}
