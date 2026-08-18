<?php

namespace App\Filament\Resources\Codecs\Schemas;

use App\Filament\Shared\FormComponents;
use Filament\Schemas\Schema;

class CodecForm
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
