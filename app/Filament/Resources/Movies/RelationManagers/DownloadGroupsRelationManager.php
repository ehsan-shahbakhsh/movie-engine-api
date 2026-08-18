<?php

namespace App\Filament\Resources\Movies\RelationManagers;

use App\Filament\Shared\FormComponents;
use App\Filament\Shared\TableColumns;
use App\Models\Movie;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DownloadGroupsRelationManager extends RelationManager
{
    protected static string $relationship = 'downloadGroups';

    protected static ?string $title = 'گروه‌های دانلود';

    protected static ?string $modelLabel = 'گروه دانلود';

    protected static ?string $pluralLabel = 'گروه‌های دانلود';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->label('عنوان')
                    ->maxLength(255),

                FormComponents::sortOrder(),
                FormComponents::status(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TableColumns::id(),

                TextColumn::make('title')
                    ->searchable()
                    ->label('عنوان')
                    ->toggleable(),

                TableColumns::sortOrder(),
                TableColumns::status(),

                TableColumns::createdAt(),
                TableColumns::updatedAt(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        $data['downloadable_type'] = Movie::class;
                        $data['downloadable_id'] = $this->ownerRecord->id;

                        return $data;
                    }),
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
