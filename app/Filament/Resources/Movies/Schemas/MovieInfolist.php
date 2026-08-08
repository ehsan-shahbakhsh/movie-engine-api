<?php

namespace App\Filament\Resources\Movies\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class MovieInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title')
                    ->label('عنوان'),

                TextEntry::make('original_title')
                    ->placeholder('-')
                    ->label('عنوان اصلی'),

                TextEntry::make('slug')
                    ->label('نامک (اسلاگ)'),

                TextEntry::make('genres.name')
                    ->badge()
                    ->label('ژانرها')
                    ->separator(),

                TextEntry::make('synopsis')
                    ->placeholder('-')
                    ->columnSpanFull()
                    ->label('خلاصه داستان'),

                TextEntry::make('release_year')
                    ->label('سال انتشار'),

                TextEntry::make('release_date')
                    ->date()
                    ->placeholder('-')
                    ->label('تاریخ انتشار'),

                TextEntry::make('duration_minutes')
                    ->placeholder('-')
                    ->label('مدت زمان')
                    ->suffix(' دقیقه'),

                TextEntry::make('ageRating.name')
                    ->badge()
                    ->label('رده سنی'),

                TextEntry::make('originalLanguage.name')
                    ->badge()
                    ->color('info')
                    ->label('زبان اصلی'),

                TextEntry::make('status')
                    ->badge()
                    ->label('وضعیت'),

                TextEntry::make('created_at')
                    ->dateTime()
                    ->label('تاریخ ایجاد')
                    ->placeholder('-')
                    ->formatStateUsing(static fn($state) => verta($state)->formatDatetime()),

                TextEntry::make('updated_at')
                    ->dateTime()
                    ->label('تاریخ آخرین بروزرسانی')
                    ->placeholder('-')
                    ->formatStateUsing(static fn($state) => verta($state)->formatDatetime()),
            ]);
    }
}
