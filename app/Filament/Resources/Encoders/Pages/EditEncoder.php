<?php

namespace App\Filament\Resources\Encoders\Pages;

use App\Filament\Resources\Encoders\EncoderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEncoder extends EditRecord
{
    protected static string $resource = EncoderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
