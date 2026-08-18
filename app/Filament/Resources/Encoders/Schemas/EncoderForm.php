<?php

namespace App\Filament\Resources\Encoders\Schemas;

use App\Filament\Shared\FormComponents;
use Filament\Schemas\Schema;

class EncoderForm
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
