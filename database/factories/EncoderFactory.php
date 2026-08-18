<?php

namespace Database\Factories;

use App\Models\Encoder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Encoder>
 */
class EncoderFactory extends Factory
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
                'PSA',
                'YIFY',
                'Pahe',
                'QxR',
                'Tigole',
                'GalaxyRG',
                'RARBG',
                'Joy',
                'MeGusta',
                'MkvCage',
                'ShAaNiG',
                'Ganool',
                'EVO',
                'RBG',
                'CMRG',
                'CtrlHD',

                'x264',
                'x265',
                'HEVC',
                'AV1',
                'HandBrake',
                'FFmpeg',
            ]),
        ];
    }
}
