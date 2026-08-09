<?php

namespace App\Filament\Resources\Movies\Pages;

use App\Filament\Resources\Movies\MovieResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateMovie extends CreateRecord
{
    protected static string $resource = MovieResource::class;

    protected function afterCreate(): void
    {
        $this->record->languages()->syncWithoutDetaching([$this->record->original_language_id]);
    }
}
