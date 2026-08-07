<?php

namespace App\Filament\Resources\Movies\Schemas;

use App\Enums\MovieStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class MovieForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('عنوان')
                    ->required()
                    ->maxLength(255),

                TextInput::make('original_title')
                    ->label('عنوان اصلی')
                    ->maxLength(255),

                TextInput::make('slug')
                    ->placeholder('در صورت خالی ماندن، خودکار تولید می‌شود')
                    ->helperText('اگر خالی بگذارید، سیستم بر اساس عنوان اصلی یک اسلاگ یکتا می‌سازد.')
                    ->label('نامک (اسلاگ)')
                    ->maxLength(255),

                Select::make('genres')
                    ->relationship('genres', 'name')
                    ->label('ژانرها')
                    ->multiple()
                    ->preload(),

                Textarea::make('synopsis')
                    ->columnSpanFull()
                    ->rows(4)
                    ->label('خلاصه داستان'),

                TextInput::make('release_year')
                    ->required()
                    ->numeric()
                    ->label('سال انتشار'),

                DatePicker::make('release_date')
                    ->label('تاریخ انتشار'),

                TextInput::make('duration_minutes')
                    ->numeric()
                    ->label('مدت زمان')
                    ->suffix(' دقیقه'),

                Select::make('status')
                    ->options(MovieStatus::class)
                    ->default(MovieStatus::Draft)
                    ->required()
                    ->label('وضعیت'),
            ]);
    }
}
