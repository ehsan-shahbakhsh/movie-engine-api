<?php

namespace App\Filament\Resources\Codecs;

use App\Filament\Resources\Codecs\Pages\CreateCodec;
use App\Filament\Resources\Codecs\Pages\EditCodec;
use App\Filament\Resources\Codecs\Pages\ListCodecs;
use App\Filament\Resources\Codecs\Schemas\CodecForm;
use App\Filament\Resources\Codecs\Tables\CodecsTable;
use App\Models\Codec;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CodecResource extends Resource
{
    protected static ?string $model = Codec::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsPointingIn;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'کدک';

    protected static ?string $pluralModelLabel = 'کدک‌ها';

    protected static ?string $navigationLabel = 'کدک‌ها';

    protected static ?int $navigationSort = 3;

    protected static string|UnitEnum|null $navigationGroup = 'تنظیمات دانلود';

    public static function form(Schema $schema): Schema
    {
        return CodecForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CodecsTable::configure($table);
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
            'index' => ListCodecs::route('/'),
            'create' => CreateCodec::route('/create'),
            'edit' => EditCodec::route('/{record}/edit'),
        ];
    }
}
