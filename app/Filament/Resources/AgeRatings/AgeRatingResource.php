<?php

namespace App\Filament\Resources\AgeRatings;

use App\Filament\Resources\AgeRatings\Pages\CreateAgeRating;
use App\Filament\Resources\AgeRatings\Pages\EditAgeRating;
use App\Filament\Resources\AgeRatings\Pages\ListAgeRatings;
use App\Filament\Resources\AgeRatings\Schemas\AgeRatingForm;
use App\Filament\Resources\AgeRatings\Tables\AgeRatingsTable;
use App\Models\AgeRating;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AgeRatingResource extends Resource
{
    protected static ?string $model = AgeRating::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'رده سنی';

    protected static ?string $pluralModelLabel = 'رده‌های سنی';

    protected static ?string $navigationLabel = 'رده‌های سنی';

    protected static ?int $navigationSort = 30;

    protected static string|UnitEnum|null $navigationGroup = 'کاتالوگ';

    // TODO: add can delete and check doesn't have any movie

    public static function form(Schema $schema): Schema
    {
        return AgeRatingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AgeRatingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAgeRatings::route('/'),
            'create' => CreateAgeRating::route('/create'),
            'edit' => EditAgeRating::route('/{record}/edit'),
        ];
    }
}
