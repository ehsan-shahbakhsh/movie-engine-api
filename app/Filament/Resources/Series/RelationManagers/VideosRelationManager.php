<?php

namespace App\Filament\Resources\Series\RelationManagers;

use App\Enums\VideoType;
use App\Filament\Shared\FormComponents;
use App\Filament\Shared\TableColumns;
use App\Models\Series;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class VideosRelationManager extends RelationManager
{
    protected static string $relationship = 'videos';

    protected static ?string $title = 'ویدیوها';

    protected static ?string $modelLabel = 'ویدیو';

    protected static ?string $pluralLabel = 'ویدیوها';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FormComponents::name(),
                Select::make('type')
                    ->options(VideoType::class)
                    ->required()
                    ->label('نوع'),

                FormComponents::status('is_official', 'رسمی'),
                FormComponents::status(),
                FormComponents::sortOrder(),

                SpatieMediaLibraryFileUpload::make('thumbnail')
                    ->collection('thumbnail')
                    ->label('تصویر کاور')
                    ->image()
                    ->imageEditor()
                    ->columnSpanFull()
                    ->required(),

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
                    ->columnSpanFull()
                    ->required(),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('نام'),
                TextEntry::make('type')
                    ->badge()
                    ->label('نوع'),

                IconEntry::make('is_official')
                    ->boolean()
                    ->label('رسمی'),
                IconEntry::make('is_active')
                    ->boolean()
                    ->label('وضعیت'),
                TextEntry::make('sort_order')
                    ->numeric()
                    ->label('ترتیب نمایش'),

                TextEntry::make('created_at')
                    ->dateTime()
                    ->label('تاریخ ایجاد')
                    ->placeholder('-')
                    ->formatStateUsing(static fn($state) => verta($state)->formatDatetime()),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->label('تاریخ آخرین بروزرسانی')
                    ->placeholder('-')
                    ->formatStateUsing(static fn($state) => verta($state)->formatDatetime()),

                Grid::make()
                    ->schema([
                        SpatieMediaLibraryImageEntry::make('thumbnail')
                            ->collection('thumbnail')
                            ->label('تصویر کاور')
                            ->conversion('thumb')
                            ->columnSpan(1),

                        TextEntry::make('video_details')
                            ->label('فایل ویدیو')
                            ->columnSpan(1)
                            ->state(static fn($record) => $record)
                            ->formatStateUsing(static function ($record) {
                                $media = $record->getFirstMedia('video');

                                $size = number_format($media->size / 1048576, 2);

                                return "حجم فایل: {$size} مگابایت | نوع فایل: {$media->mime_type}";
                            })
                            ->suffixAction(
                                Action::make('download')
                                    ->label('دانلود / مشاهده ویدیو')
                                    ->icon(Heroicon::ArrowDownTray)
                                    ->url(static fn($record) => $record->getFirstMediaUrl('video'), shouldOpenInNewTab: true)
                                    ->visible(static fn($record) => $record->hasMedia('video'))
                            ),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->reorderable('sort_order')
            ->columns([
                TableColumns::id(),

                TableColumns::name(),
                TextColumn::make('type')
                    ->badge()
                    ->label('نوع'),

                TableColumns::status('is_official', 'رسمی'),
                TableColumns::status(),
                TableColumns::sortOrder(),

                TableColumns::createdAt(),
                TableColumns::updatedAt(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('نوع')
                    ->options(VideoType::class)
                    ->multiple(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        $data['videoable_type'] = Series::class;
                        $data['videoable_id'] = $this->ownerRecord->id;

                        return $data;
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
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
