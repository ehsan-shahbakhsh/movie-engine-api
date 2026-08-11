<?php

namespace App\Filament\Resources\People\Schemas;

use App\Filament\Shared\FormComponents;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PersonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FormComponents::name(),
                FormComponents::name('original_name', 'نام اصلی', false),

                FormComponents::slug(),

                SpatieMediaLibraryFileUpload::make('profile')
                    ->label('پروفایل')
                    ->collection('profile')
                    ->image()
                    ->imageEditor(),

                Hidden::make('birth_date_is_jalali'),
                Hidden::make('death_date_is_jalali'),

                DatePicker::make('birth_date_jalali')
                    ->required()
                    ->label('تاریخ تولد')
                    ->jalali()
                    ->visible(static fn(Get $get): bool => (bool)$get('birth_date_is_jalali'))
                    ->suffixAction(
                        Action::make('change_birth_calendar')
                            ->icon(Heroicon::OutlinedArrowPath)
                            ->action(function (Set $set) {
                                $set('birth_date_is_jalali', false);
                            }),
                    ),

                DatePicker::make('birth_date_georgian')
                    ->required()
                    ->label('تاریخ تولد')
                    ->visible(static fn(Get $get): bool => !$get('birth_date_is_jalali'))
                    ->suffixAction(
                        Action::make('change_birth_calendar')
                            ->icon(Heroicon::OutlinedArrowPath)
                            ->action(function (Set $set) {
                                $set('birth_date_is_jalali', true);
                            }),
                    ),

                DatePicker::make('death_date_jalali')
                    ->label('تاریخ فوت')
                    ->jalali()
                    ->visible(static fn(Get $get): bool => (bool)$get('death_date_is_jalali'))
                    ->suffixAction(
                        Action::make('change_death_calendar')
                            ->icon(Heroicon::OutlinedArrowPath)
                            ->action(function (Set $set) {
                                $set('death_date_is_jalali', false);
                            }),
                    ),

                DatePicker::make('death_date_georgian')
                    ->label('تاریخ فوت')
                    ->visible(static fn(Get $get): bool => !$get('death_date_is_jalali'))
                    ->suffixAction(
                        Action::make('change_death_calendar')
                            ->icon(Heroicon::OutlinedArrowPath)
                            ->action(function (Set $set) {
                                $set('death_date_is_jalali', true);
                            }),
                    ),

                Textarea::make('biography')
                    ->rows(4)
                    ->columnSpanFull()
                    ->label('بیوگرافی'),
            ]);
    }
}
