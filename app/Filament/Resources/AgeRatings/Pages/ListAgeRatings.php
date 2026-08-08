<?php

namespace App\Filament\Resources\AgeRatings\Pages;

use App\Filament\Resources\AgeRatings\AgeRatingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAgeRatings extends ListRecords
{
    protected static string $resource = AgeRatingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
