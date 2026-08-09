<?php

namespace Database\Factories;

use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Person>
 */
class PersonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->name();
        $originalName = fake()->optional()->name();
        $slug = Str::slug($originalName ?? $name) . '-' . fake()->unique()->numberBetween(1, 999999);

        $birthDate = fake()->dateTimeBetween('-100 years', '-10 years');

        return [
            'name' => $name,
            'original_name' => $originalName,
            'slug' => $slug,

            'birth_date' => $birthDate,
            'death_date' => fake()->optional(0.2)->dateTimeBetween($birthDate),

            'biography' => fake()->optional()->realText(),
        ];
    }
}
