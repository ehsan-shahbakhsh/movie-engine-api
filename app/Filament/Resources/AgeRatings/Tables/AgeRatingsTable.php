<?php

namespace App\Filament\Resources\AgeRatings\Tables;

use App\Filament\Shared\TableColumns;
use App\Models\AgeRating;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
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
                DeleteAction::make()
                    ->before(function (AgeRating $record, DeleteAction $action) {
                        if ($record->movies()->exists()) {
                            Notification::make()
                                ->danger()
                                ->title('امکان حذف وجود ندارد')
                                ->body("این رده سنی به {$record->movies()->count()} فیلم متصل است و ابتدا باید ارتباط آن‌ها را بردارید.")
                                ->persistent()
                                ->send();

                            $action->halt();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                ]),
            ]);
    }
}
