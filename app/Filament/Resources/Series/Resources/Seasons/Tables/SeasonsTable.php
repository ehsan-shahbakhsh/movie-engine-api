<?php

namespace App\Filament\Resources\Series\Resources\Seasons\Tables;

use App\Filament\Shared\TableColumns;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SeasonsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('season_number')
            ->columns([
                TableColumns::id(),

                TextColumn::make('season_number')
                    ->sortable()
                    ->label('شماره فصل')
                    ->toggleable(),

                TextColumn::make('title')
                    ->searchable()
                    ->label('عنوان')
                    ->toggleable(),

                TextColumn::make('release_date')
                    ->date()
                    ->sortable()
                    ->label('تاریخ انتشار')
                    ->formatStateUsing(static fn($state) => $state ? $state->toDateString() : null)
                    ->toggleable(),

                TextColumn::make('end_date')
                    ->date()
                    ->sortable()
                    ->label('تاریخ پایان')
                    ->formatStateUsing(static fn($state) => $state ? $state->toDateString() : null)
                    ->toggleable(),

                TableColumns::createdAt(),
                TableColumns::updatedAt(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
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
