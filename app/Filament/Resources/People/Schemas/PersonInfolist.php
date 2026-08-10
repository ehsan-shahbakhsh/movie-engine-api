<?php

namespace App\Filament\Resources\People\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PersonInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('نام'),

                TextEntry::make('original_name')
                    ->placeholder('-')
                    ->label('نام اصلی'),

                TextEntry::make('slug')
                    ->label('نامک (اسلاگ)'),

                TextEntry::make('birth_date')
                    ->date()
                    ->formatStateUsing(static function ($state) {
                        return sprintf(
                            '%s - %s',
                            verta($state)->formatDate(),
                            $state->toDateString(),
                        );
                    })
                    ->label('تاریخ تولد'),

                TextEntry::make('death_date')
                    ->date()
                    ->formatStateUsing(static function ($state) {
                        return sprintf(
                            '%s - %s',
                            verta($state)->formatDate(),
                            $state->toDateString(),
                        );
                    })
                    ->formatStateUsing(static fn($state) => verta($state)->formatDate())
                    ->label('تاریخ فوت')
                    ->placeholder('زنده'),

                TextEntry::make('biography')
                    ->placeholder('-')
                    ->columnSpanFull()
                    ->label('بیوگرافی'),

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
