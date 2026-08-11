<?php

namespace App\Filament\Resources\People\Tables;

use App\Filament\Shared\TableColumns;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PeopleTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TableColumns::id(),

                SpatieMediaLibraryImageColumn::make('profile')
                    ->collection('profile')
                    ->conversion('thumb')
                    ->circular()
                    ->label('پروفایل'),

                TableColumns::name(),
                TableColumns::name('original_name', 'نام اصلی'),
                TableColumns::slug(),

                TextColumn::make('biography')
                    ->searchable()
                    ->label('بیوگرافی')
                    ->limit(50)
                    ->tooltip(static fn($state) => $state)
                    ->toggleable(),

                TextColumn::make('birth_date')
                    ->label('تاریخ تولد')
                    ->date()
                    ->sortable()
                    ->formatStateUsing(static fn($state) => verta($state)->formatDate() . ' شمسی')
                    ->description(static fn($state) => $state->toDateString() . ' میلادی')
                    ->toggleable(),
                TextColumn::make('death_date')
                    ->label('تاریخ فوت')
                    ->date()
                    ->sortable()
                    ->formatStateUsing(static fn($state) => verta($state)->formatDate() . ' شمسی')
                    ->description(static fn($state) => $state?->toDateString() . ' میلادی')
                    ->placeholder('زنده')
                    ->toggleable(),

                TableColumns::createdAt(),
                TableColumns::updatedAt(),
            ])
            ->filters([
                TernaryFilter::make('is_alive')
                    ->label('وضعیت حیات')
                    ->placeholder('همه اشخاص')
                    ->trueLabel('زنده‌ها')
                    ->falseLabel('فوت‌شده‌ها')
                    ->queries(
                        true: static fn(Builder $query) => $query->whereNull('death_date'),
                        false: static fn(Builder $query) => $query->whereNotNull('death_date'),
                    ),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
