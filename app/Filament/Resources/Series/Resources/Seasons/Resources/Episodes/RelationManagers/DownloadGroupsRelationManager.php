<?php

namespace App\Filament\Resources\Series\Resources\Seasons\Resources\Episodes\RelationManagers;

use App\Filament\Shared\FormComponents;
use App\Filament\Shared\TableColumns;
use App\Models\Episode;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
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

                Repeater::make('downloadLinks')
                    ->label('لینک‌های دانلود')
                    ->relationship()
                    ->schema([
                        Select::make('quality_id')
                            ->relationship('quality', 'name')
                            ->required()
                            ->label('کیفیت'),

                        Select::make('encoder_id')
                            ->relationship('encoder', 'name')
                            ->label('انکودر'),

                        Select::make('codec_id')
                            ->relationship('codec', 'name')
                            ->label('کدک'),

                        SpatieMediaLibraryFileUpload::make('video')
                            ->collection('video')
                            ->label('فایل ویدیو')
                            ->acceptedFileTypes([
                                'video/mp4',
                                'video/webm',
                                'video/ogg',
                                'video/quicktime',
                                'video/x-msvideo',
                                'video/x-matroska',
                                'video/x-flv',
                            ])
                            ->required(),
                    ])
                    ->columns(2)
                    ->addActionLabel('افزودن کیفیت جدید')
                    ->columnSpanFull(),
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

                TextColumn::make('download_links_count')
                    ->sortable()
                    ->counts('downloadLinks')
                    ->label('تعداد لینک‌های دانلود')
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
                        $data['downloadable_type'] = Episode::class;
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
