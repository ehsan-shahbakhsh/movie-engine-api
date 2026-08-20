<?php

namespace App\Filament\Resources\Series\Resources\Seasons\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SeasonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('season_number')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->label('شماره فصل')
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn($rule) => $rule->where('series_id', $schema->getLivewire()->parentRecord->id),
                    ),

                TextInput::make('title')
                    ->label('عنوان')
                    ->maxLength(255),

                DatePicker::make('release_date')
                    ->label('تاریخ انتشار'),

                DatePicker::make('end_date')
                    ->label('تاریخ اتمام'),
            ]);
    }
}
