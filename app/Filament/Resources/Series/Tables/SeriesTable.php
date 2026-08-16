<?php

namespace App\Filament\Resources\Series\Tables;

use App\Enums\SeriesProductionStatus;
use App\Enums\SeriesPublishStatus;
use App\Filament\Shared\TableColumns;
use App\Models\Series;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SeriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TableColumns::id(),

                SpatieMediaLibraryImageColumn::make('poster')
                    ->collection('poster')
                    ->conversion('thumb')
                    ->circular()
                    ->label('پوستر')
                    ->toggleable(),

                TextColumn::make('title')
                    ->label('عنوان سریال')
                    ->searchable(['title', 'original_title'])
                    ->description(static fn(Series $record): ?string => $record->original_title)
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('slug')
                    ->searchable()
                    ->label('نامک (اسلاگ)')
                    ->toggleable(),

                TextColumn::make('genres.name')
                    ->badge()
                    ->separator()
                    ->label('ژانرها')
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

                TextColumn::make('end_date')
                    ->date()
                    ->sortable()
                    ->label('تاریخ اتمام')
                    ->formatStateUsing(static fn($state) => $state ? $state->toDateString() : null)
                    ->toggleable(),

                TextColumn::make('ageRating.name')
                    ->badge()
                    ->label('رده سنی')
                    ->toggleable(),

                TextColumn::make('originalLanguage.name')
                    ->badge()
                    ->color('info')
                    ->label('زبان اصلی')
                    ->toggleable(),

                TextColumn::make('publish_status')
                    ->badge()
                    ->label('وضعیت انتشار')
                    ->toggleable(),

                TextColumn::make('production_status')
                    ->badge()
                    ->label('وضعیت پخش')
                    ->toggleable(),

                TableColumns::createdAt(),
                TableColumns::updatedAt(),
            ])
            ->filters([
                SelectFilter::make('publish_status')
                    ->label('وضعیت انتشار')
                    ->options(SeriesPublishStatus::class)
                    ->multiple(),

                SelectFilter::make('production_status')
                    ->label('وضعیت پخش')
                    ->options(SeriesProductionStatus::class)
                    ->multiple(),

                SelectFilter::make('ageRating')
                    ->label('رده سنی')
                    ->relationship('ageRating', 'name')
                    ->multiple(),

                SelectFilter::make('originalLanguage')
                    ->label('زبان اصلی')
                    ->relationship('originalLanguage', 'name')
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
