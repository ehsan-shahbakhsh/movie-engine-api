<?php

namespace App\Filament\Resources\Series\RelationManagers;

use App\Filament\Shared\TableColumns;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SeasonsRelationManager extends RelationManager
{
    protected static string $relationship = 'seasons';

    protected static ?string $title = 'فصل‌ها';

    protected static ?string $modelLabel = 'فصل';

    protected static ?string $pluralModelLabel = 'فصل‌ها';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('season_number')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->label('شماره فصل')
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn($rule) => $rule->where('series_id', $this->ownerRecord->id),
                    ),

                TextInput::make('title')
                    ->label('عنوان')
                    ->maxLength(255),

                DatePicker::make('release_date')
                    ->label('تاریخ انتشار'),

                DatePicker::make('end_date')
                    ->label('تاریخ اتمام'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('season_number')
            ->recordTitleAttribute('season_number')
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
            ->headerActions([
                CreateAction::make(),
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
