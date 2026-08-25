<?php

namespace Database\Factories;

use App\Enums\CommentStatus;
use App\Models\Comment;
use App\Models\Movie;
use App\Models\Series;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $commentableClass = fake()->randomElement([Movie::class, Series::class]);

        return [
            'user_id' => User::factory(),

            'parent_id' => null,

            'commentable_id' => $commentableClass::factory(),
            'commentable_type' => $commentableClass,

            'body' => fake()->paragraph(),

            'is_official' => false,
            'is_spoiler' => fake()->boolean(),

            'status' => fake()->randomElement(CommentStatus::cases()),
        ];
    }

    public function reply(Comment $parent): static
    {
        return $this->state(fn(array $attributes) => [
            'parent_id' => $parent->id,

            'commentable_id' => $parent->commentable_id,
            'commentable_type' => $parent->commentable_type,
        ]);
    }
}
