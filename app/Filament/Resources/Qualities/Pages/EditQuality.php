<?php

namespace App\Filament\Resources\Qualities\Pages;

use App\Filament\Resources\Qualities\QualityResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditQuality extends EditRecord
{
    protected static string $resource = QualityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
