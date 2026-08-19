<?php

namespace Database\Factories;

use App\Models\Codec;
use App\Models\DownloadGroup;
use App\Models\DownloadLink;
use App\Models\Encoder;
use App\Models\Quality;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DownloadLink>
 */
class DownloadLinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'download_group_id' => DownloadGroup::factory(),

            'quality_id' => Quality::factory(),
            'encoder_id' => Encoder::factory(),
            'codec_id' => Codec::factory(),
        ];
    }
}
