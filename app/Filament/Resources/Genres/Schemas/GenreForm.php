<?php

namespace App\Filament\Resources\Genres\Schemas;

use App\Filament\Shared\FormComponents;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class GenreForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FormComponents::name()
                    ->unique(ignoreRecord: true),

                FormComponents::slug(),

                Textarea::make('description')
                    ->rows(4)
                    ->columnSpanFull()
                    ->label('توضیحات'),

                FormComponents::status(),

                FormComponents::sortOrder(),
            ]);
    }
}
