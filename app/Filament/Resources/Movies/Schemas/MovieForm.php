<?php

namespace App\Filament\Resources\Movies\Schemas;

use App\Enums\MovieStatus;
use App\Filament\Shared\FormComponents;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

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

                Select::make('age_rating_id')
                    ->label('رده سنی')
                    ->relationship('ageRating', 'name')
                    ->preload()
                    ->createOptionForm([
                        FormComponents::name()->unique(),
                    ]),

                Select::make('original_language_id')
                    ->label('زبان اصلی')
                    ->relationship('originalLanguage', 'name')
                    ->searchable()
                    ->required(),

                Select::make('languages')
                    ->relationship('languages', 'name')
                    ->label('زبان‌ها')
                    ->multiple(),

                Select::make('countries')
                    ->relationship('countries', 'name')
                    ->label('کشورهای سازنده')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->optionsLimit(250),

                Select::make('status')
                    ->options(MovieStatus::class)
                    ->default(MovieStatus::Draft)
                    ->required()
                    ->label('وضعیت'),

                Section::make('تصاویر و رسانه‌ها')
                    ->description('پوستر، تصویر پس‌زمینه، لوگو و گالری تصاویر فیلم را اینجا آپلود کنید.')
                    ->icon(Heroicon::OutlinedPhoto)
                    ->columns()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('poster')
                            ->label('پوستر فیلم (Poster)')
                            ->collection('poster')
                            ->image()
                            ->imageEditor()
                            ->required()
                            ->columnSpan(1),

                        SpatieMediaLibraryFileUpload::make('logo')
                            ->label('لوگوی فیلم (Logo)')
                            ->collection('logo')
                            ->image()
                            ->imageEditor()
                            ->columnSpan(1),

                        SpatieMediaLibraryFileUpload::make('backdrop')
                            ->label('تصویر پس‌زمینه (Backdrop)')
                            ->collection('backdrop')
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull(),

                        SpatieMediaLibraryFileUpload::make('gallery')
                            ->label('گالری تصاویر (Gallery)')
                            ->collection('gallery')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->panelLayout('grid')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
