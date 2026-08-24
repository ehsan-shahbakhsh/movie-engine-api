<?php

namespace Database\Factories;

use App\Models\Movie;
use App\Models\Series;
use App\Models\User;
use App\Models\Watchlist;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Watchlist>
 */
class WatchlistFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $watchableClass = fake()->randomElement([Movie::class, Series::class]);

        return [
            'user_id' => User::factory(),

            'watchable_id' => $watchableClass::factory(),
            'watchable_type' => $watchableClass,
        ];
    }
}
