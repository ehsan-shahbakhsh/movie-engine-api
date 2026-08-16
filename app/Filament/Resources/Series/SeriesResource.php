<?php

namespace App\Filament\Resources\Series;

use App\Filament\Resources\Series\Pages\CreateSeries;
use App\Filament\Resources\Series\Pages\EditSeries;
use App\Filament\Resources\Series\Pages\ListSeries;
use App\Filament\Resources\Series\Pages\ViewSeries;
use App\Filament\Resources\Series\Schemas\SeriesForm;
use App\Filament\Resources\Series\Schemas\SeriesInfolist;
use App\Filament\Resources\Series\Tables\SeriesTable;
use App\Models\Series;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class SeriesResource extends Resource
{
    protected static ?string $model = Series::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTv;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $modelLabel = 'سریال';

    protected static ?string $pluralModelLabel = 'سریال‌ها';

    protected static ?string $navigationLabel = 'سریال‌ها';

    protected static ?int $navigationSort = 5;

    protected static string|UnitEnum|null $navigationGroup = 'کاتالوگ';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with([
            'ageRating',
            'originalLanguage',
            'media' => static fn($query) => $query->where('collection_name', 'poster'),
            'genres',
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return SeriesForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SeriesInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SeriesTable::configure($table);
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
            'index' => ListSeries::route('/'),
            'create' => CreateSeries::route('/create'),
            'view' => ViewSeries::route('/{record}'),
            'edit' => EditSeries::route('/{record}/edit'),
        ];
    }
}
