<?php

namespace App\Filament\Resources\Encoders\Pages;

use App\Filament\Resources\Encoders\EncoderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEncoders extends ListRecords
{
    protected static string $resource = EncoderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
