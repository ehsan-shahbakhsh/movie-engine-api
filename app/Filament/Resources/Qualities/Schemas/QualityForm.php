<?php

namespace App\Filament\Resources\Qualities\Schemas;

use App\Filament\Shared\FormComponents;
use Filament\Schemas\Schema;

class QualityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FormComponents::name()
                    ->unique(ignoreRecord: true),
            ]);
    }
}
