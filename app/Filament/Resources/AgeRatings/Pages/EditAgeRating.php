<?php

namespace App\Filament\Resources\AgeRatings\Pages;

use App\Filament\Resources\AgeRatings\AgeRatingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAgeRating extends EditRecord
{
    protected static string $resource = AgeRatingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
