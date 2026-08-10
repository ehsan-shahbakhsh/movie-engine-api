<?php

namespace App\Filament\Resources\People\Pages;

use App\Filament\Resources\People\PersonResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePerson extends CreateRecord
{
    protected static string $resource = PersonResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($data['birth_date_is_jalali']) {
            $data['birth_date'] = $data['birth_date_jalali'];
        } else {
            $data['birth_date'] = $data['birth_date_georgian'];
        }

        if ($data['death_date_is_jalali']) {
            $data['death_date'] = $data['death_date_jalali'] ?? null;
        } else {
            $data['death_date'] = $data['death_date_georgian'] ?? null;
        }

        return $data;
    }
}
