<?php

namespace App\Filament\Resources\Series\Resources\Seasons\Resources\Episodes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EpisodeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('episode_number')
                    ->numeric()
                    ->label('شماره قسمت'),

                TextEntry::make('title')
                    ->placeholder('-')
                    ->label('عنوان'),

                TextEntry::make('synopsis')
                    ->placeholder('-')
                    ->columnSpanFull()
                    ->label('خلاصه داستان'),

                TextEntry::make('release_date')
                    ->date()
                    ->placeholder('-')
                    ->label('تاریخ پخش'),

                TextEntry::make('duration_minutes')
                    ->placeholder('-')
                    ->label('مدت زمان')
                    ->suffix(' دقیقه'),

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
