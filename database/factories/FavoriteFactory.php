<?php

namespace Database\Factories;

use App\Models\Favorite;
use App\Models\Movie;
use App\Models\Series;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Favorite>
 */
class FavoriteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $favoritableClass = fake()->randomElement([Movie::class, Series::class]);

        return [
            'user_id' => User::factory(),

            'favoritable_id' => $favoritableClass::factory(),
            'favoritable_type' => $favoritableClass,
        ];
    }
}
