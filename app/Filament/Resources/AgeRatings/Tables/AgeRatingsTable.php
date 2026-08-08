<?php

namespace App\Filament\Resources\AgeRatings\Tables;

use App\Filament\Shared\TableColumns;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class AgeRatingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TableColumns::id(),

                TableColumns::name(),

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
                ]),
            ]);
    }
}
