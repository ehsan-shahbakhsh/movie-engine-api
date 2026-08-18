<?php

namespace App\Filament\Resources\Codecs\Pages;

use App\Filament\Resources\Codecs\CodecResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCodec extends EditRecord
{
    protected static string $resource = CodecResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
