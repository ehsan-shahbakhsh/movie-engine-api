<?php

namespace App\Filament\Resources\AgeRatings\Pages;

use App\Filament\Resources\AgeRatings\AgeRatingResource;
use App\Models\AgeRating;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditAgeRating extends EditRecord
{
    protected static string $resource = AgeRatingResource::class;

    protected function getHeaderActions(): array
    {
        return [
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
        ];
    }
}
