<?php

namespace App\Filament\Resources\Movies\Tables;

use App\Enums\MovieStatus;
use App\Filament\Shared\TableColumns;
use App\Models\Movie;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MoviesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TableColumns::id(),

                TextColumn::make('title')
                    ->label('عنوان فیلم')
                    ->searchable(['title', 'original_title'])
                    ->description(static fn(Movie $record): ?string => $record->original_title)
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('slug')
                    ->searchable()
                    ->label('نامک (اسلاگ)')
                    ->toggleable(),

                TextColumn::make('release_year')
                    ->sortable()
                    ->label('سال انتشار')
                    ->toggleable(),

                TextColumn::make('release_date')
                    ->date()
                    ->sortable()
                    ->label('تاریخ انتشار')
                    ->formatStateUsing(static fn($state) => $state ? $state->toDateString() : null)
                    ->toggleable(),

                TextColumn::make('duration_minutes')
                    ->sortable()
                    ->label('مدت زمان')
                    ->suffix(' دقیقه')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('status')
                    ->badge()
                    ->label('وضعیت')
                    ->toggleable(),

                TableColumns::createdAt(),
                TableColumns::updatedAt(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options(MovieStatus::class)
                    ->multiple(),
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
