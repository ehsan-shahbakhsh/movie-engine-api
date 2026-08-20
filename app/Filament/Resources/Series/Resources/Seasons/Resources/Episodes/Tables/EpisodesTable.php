<?php

namespace App\Filament\Resources\Series\Resources\Seasons\Resources\Episodes\Tables;

use App\Filament\Shared\TableColumns;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EpisodesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('episode_number')
            ->columns([
                TableColumns::id(),

                TextColumn::make('episode_number')
                    ->sortable()
                    ->label('شماره قسمت')
                    ->toggleable(),

                TextColumn::make('title')
                    ->searchable()
                    ->label('عنوان')
                    ->toggleable(),

                TextColumn::make('release_date')
                    ->date()
                    ->sortable()
                    ->label('تاریخ پخش')
                    ->formatStateUsing(static fn($state) => $state ? $state->toDateString() : null)
                    ->toggleable(),

                TextColumn::make('duration_minutes')
                    ->sortable()
                    ->label('مدت زمان')
                    ->suffix(' دقیقه')
                    ->placeholder('-')
                    ->toggleable(),

                TableColumns::createdAt(),
                TableColumns::updatedAt(),
            ])
            ->filters([
                //
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
