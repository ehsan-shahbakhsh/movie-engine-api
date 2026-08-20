<?php

namespace App\Filament\Resources\Series\Resources\Seasons;

use App\Filament\Resources\Series\Resources\Seasons\Pages\CreateSeason;
use App\Filament\Resources\Series\Resources\Seasons\Pages\EditSeason;
use App\Filament\Resources\Series\Resources\Seasons\RelationManagers\EpisodesRelationManager;
use App\Filament\Resources\Series\Resources\Seasons\Schemas\SeasonForm;
use App\Filament\Resources\Series\Resources\Seasons\Tables\SeasonsTable;
use App\Filament\Resources\Series\SeriesResource;
use App\Models\Season;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SeasonResource extends Resource
{
    protected static ?string $model = Season::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $parentResource = SeriesResource::class;

    protected static ?string $recordTitleAttribute = 'season_number';

    protected static ?string $title = 'فصل‌ها';

    protected static ?string $modelLabel = 'فصل';

    protected static ?string $pluralModelLabel = 'فصل‌ها';

    public static function form(Schema $schema): Schema
    {
        return SeasonForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SeasonsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            EpisodesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'create' => CreateSeason::route('/create'),
            'edit' => EditSeason::route('/{record}/edit'),
        ];
    }
}
