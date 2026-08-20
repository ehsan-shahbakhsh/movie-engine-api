<?php

namespace App\Filament\Resources\Series\Resources\Seasons\Resources\Episodes\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class EpisodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('episode_number')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->label('شماره قسمت')
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn($rule) => $rule->where('season_id', $schema->getLivewire()->parentRecord->id),
                    ),

                TextInput::make('title')
                    ->label('عنوان')
                    ->maxLength(255),

                Textarea::make('synopsis')
                    ->columnSpanFull()
                    ->rows(4)
                    ->label('خلاصه داستان'),

                DatePicker::make('air_date')
                    ->label('تاریخ پخش'),

                TextInput::make('duration_minutes')
                    ->numeric()
                    ->label('مدت زمان')
                    ->suffix(' دقیقه'),
            ]);
    }
}
