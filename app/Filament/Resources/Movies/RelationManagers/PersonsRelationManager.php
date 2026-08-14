<?php

namespace App\Filament\Resources\Movies\RelationManagers;

use App\Enums\MoviePersonDepartment;
use App\Filament\Shared\FormComponents;
use App\Filament\Shared\TableColumns;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PersonsRelationManager extends RelationManager
{
    protected static string $relationship = 'persons';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $title = 'اشخاص';

    protected static ?string $modelLabel = 'شخص';

    protected static ?string $pluralLabel = 'اشخاص';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TableColumns::id(),

                TableColumns::name(),
                TableColumns::name('original_name', 'نام اصلی'),

                TextColumn::make('pivot.department')
                    ->searchable()
                    ->label('بخش')
                    ->badge()
                    ->toggleable(),

                TableColumns::name('pivot.job', 'شغل'),
                TableColumns::name('pivot.character_name', 'نام شخصیت'),

                TableColumns::createdAt('pivot.created_at'),
                TableColumns::updatedAt('pivot.updated_at'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->schema(static fn(AttachAction $action): array => [
                        $action->getRecordSelect(),

                        Select::make('department')
                            ->required()
                            ->label('بخش')
                            ->options(MoviePersonDepartment::class),

                        FormComponents::name('job', 'شغل', false),
                        FormComponents::name('character_name', 'نام شخصیت', false),
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->schema([
                        Select::make('department')
                            ->required()
                            ->label('بخش')
                            ->options(MoviePersonDepartment::class),

                        FormComponents::name('job', 'شغل', false),
                        FormComponents::name('character_name', 'نام شخصیت', false),
                    ]),
                DetachAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
