<?php

namespace App\Filament\Resources\Series\Schemas;

use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class SeriesInfolist
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

                TextEntry::make('end_date')
                    ->date()
                    ->placeholder('-')
                    ->label('تاریخ اتمام'),

                TextEntry::make('ageRating.name')
                    ->badge()
                    ->label('رده سنی'),

                TextEntry::make('originalLanguage.name')
                    ->badge()
                    ->color('info')
                    ->label('زبان اصلی'),

                TextEntry::make('publish_status')
                    ->badge()
                    ->label('وضعیت انتشار'),

                TextEntry::make('production_status')
                    ->badge()
                    ->label('وضعیت پخش'),

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

                Section::make('تصاویر و رسانه‌ها')
                    ->icon(Heroicon::OutlinedPhoto)
                    ->columns()
                    ->schema([
                        SpatieMediaLibraryImageEntry::make('poster')
                            ->collection('poster')
                            ->conversion('medium')
                            ->label('پوستر اصلی')
                            ->columnSpan(1),

                        SpatieMediaLibraryImageEntry::make('logo')
                            ->collection('logo')
                            ->label('لوگوی فیلم')
                            ->columnSpan(1),

                        SpatieMediaLibraryImageEntry::make('backdrop')
                            ->collection('backdrop')
                            ->conversion('backdrop')
                            ->label('تصویر پس‌زمینه (Backdrop)')
                            ->columnSpanFull(),

                        SpatieMediaLibraryImageEntry::make('gallery')
                            ->collection('gallery')
                            ->label('گالری تصاویر')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
