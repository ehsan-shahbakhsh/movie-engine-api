<?php

namespace App\Filament\Resources\AgeRatings\Schemas;

use App\Filament\Shared\FormComponents;
use Filament\Schemas\Schema;

class AgeRatingForm
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
