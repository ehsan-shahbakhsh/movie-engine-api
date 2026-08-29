<?php

namespace Database\Factories;

use App\Enums\ReactionType;
use App\Models\Movie;
use App\Models\Reaction;
use App\Models\Series;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reaction>
 */
class ReactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $reactionableClass = fake()->randomElement([Movie::class, Series::class]);

        return [
            'user_id' => User::factory(),

            'reactionable_id' => $reactionableClass::factory(),
            'reactionable_type' => $reactionableClass,

            'type' => fake()->randomElement(ReactionType::cases()),
        ];
    }
}
