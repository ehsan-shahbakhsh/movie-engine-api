<?php

namespace App\Filament\Resources\Codecs\Pages;

use App\Filament\Resources\Codecs\CodecResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCodecs extends ListRecords
{
    protected static string $resource = CodecResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
