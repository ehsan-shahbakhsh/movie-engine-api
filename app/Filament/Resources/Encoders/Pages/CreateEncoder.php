<?php

namespace App\Filament\Resources\Encoders\Pages;

use App\Filament\Resources\Encoders\EncoderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEncoder extends CreateRecord
{
    protected static string $resource = EncoderResource::class;
}
