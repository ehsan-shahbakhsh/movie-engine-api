<?php

namespace App\Filament\Resources\Encoders;

use App\Filament\Resources\Encoders\Pages\CreateEncoder;
use App\Filament\Resources\Encoders\Pages\EditEncoder;
use App\Filament\Resources\Encoders\Pages\ListEncoders;
use App\Filament\Resources\Encoders\Schemas\EncoderForm;
use App\Filament\Resources\Encoders\Tables\EncodersTable;
use App\Models\Encoder;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EncoderResource extends Resource
{
    protected static ?string $model = Encoder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCpuChip;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'انکودر';

    protected static ?string $pluralModelLabel = 'انکودرها';

    protected static ?string $navigationLabel = 'انکودرها';

    protected static ?int $navigationSort = 2;

    protected static string|UnitEnum|null $navigationGroup = 'تنظیمات دانلود';

    public static function form(Schema $schema): Schema
    {
        return EncoderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EncodersTable::configure($table);
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
            'index' => ListEncoders::route('/'),
            'create' => CreateEncoder::route('/create'),
            'edit' => EditEncoder::route('/{record}/edit'),
        ];
    }
}
