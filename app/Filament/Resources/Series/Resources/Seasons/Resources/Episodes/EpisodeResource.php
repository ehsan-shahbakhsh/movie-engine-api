<?php

namespace App\Filament\Resources\Series\Resources\Seasons\Resources\Episodes;

use App\Filament\Resources\Series\Resources\Seasons\Resources\Episodes\Pages\CreateEpisode;
use App\Filament\Resources\Series\Resources\Seasons\Resources\Episodes\Pages\EditEpisode;
use App\Filament\Resources\Series\Resources\Seasons\Resources\Episodes\Pages\ViewEpisode;
use App\Filament\Resources\Series\Resources\Seasons\Resources\Episodes\RelationManagers\DownloadGroupsRelationManager;
use App\Filament\Resources\Series\Resources\Seasons\Resources\Episodes\Schemas\EpisodeForm;
use App\Filament\Resources\Series\Resources\Seasons\Resources\Episodes\Schemas\EpisodeInfolist;
use App\Filament\Resources\Series\Resources\Seasons\Resources\Episodes\Tables\EpisodesTable;
use App\Filament\Resources\Series\Resources\Seasons\SeasonResource;
use App\Models\Episode;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EpisodeResource extends Resource
{
    protected static ?string $model = Episode::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $parentResource = SeasonResource::class;

    protected static ?string $recordTitleAttribute = 'episode_number';

    protected static ?string $title = 'قسمت‌ها';

    protected static ?string $modelLabel = 'قسمت';

    protected static ?string $pluralLabel = 'قسمت‌ها';

    public static function form(Schema $schema): Schema
    {
        return EpisodeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EpisodeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EpisodesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            DownloadGroupsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'create' => CreateEpisode::route('/create'),
            'view' => ViewEpisode::route('/{record}'),
            'edit' => EditEpisode::route('/{record}/edit'),
        ];
    }
}
