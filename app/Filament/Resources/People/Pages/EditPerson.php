<?php

namespace App\Filament\Resources\People\Pages;

use App\Filament\Resources\People\PersonResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPerson extends EditRecord
{
    protected static string $resource = PersonResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['birth_date_jalali'] = $data['birth_date'];
        $data['birth_date_georgian'] = $data['birth_date'];

        if ($data['death_date']) {
            $data['death_date_jalali'] = $data['death_date'];
            $data['death_date_georgian'] = $data['death_date'];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
