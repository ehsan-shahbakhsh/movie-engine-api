<?php

namespace Database\Factories;

use App\Models\Codec;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Codec>
 */
class CodecFactory extends Factory
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
                'x264',
                'x265',
                'HEVC',
                'AV1',
                'VP9',
                'AVC',
            ]),
        ];
    }
}
