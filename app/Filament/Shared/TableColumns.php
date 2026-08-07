<?php

namespace App\Filament\Shared;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;

final class TableColumns
{
    public static function id(): TextColumn
    {
        return TextColumn::make('id')
            ->sortable()
            ->label('شناسه')
            ->toggleable(isToggledHiddenByDefault: true);
    }

    public static function sortOrder(string $name = 'sort_order', string $label = 'ترتیب نمایش'): TextInputColumn
    {
        return TextInputColumn::make($name)
            ->label($label)
            ->inputMode('number')
            ->rules(['required', 'int', 'min:0'])
            ->step(1)
            ->sortable()
            ->extraAttributes(['style' => 'max-width:80px'])
            ->toggleable();
    }

    public static function status(string $name = 'is_active', string $label = 'وضعیت'): ToggleColumn
    {
        return ToggleColumn::make($name)
            ->label($label)
            ->onColor('success')
            ->offColor('danger')
            ->toggleable();
    }

    public static function createdAt(): TextColumn
    {
        return TextColumn::make('created_at')
            ->dateTime()
            ->sortable()
            ->label('تاریخ ایجاد')
            ->formatStateUsing(static fn($state) => verta($state)->formatDatetime())
            ->toggleable(isToggledHiddenByDefault: true);
    }

    public static function updatedAt(): TextColumn
    {
        return TextColumn::make('updated_at')
            ->dateTime()
            ->sortable()
            ->label('تاریخ آخرین بروزرسانی')
            ->formatStateUsing(static fn($state) => verta($state)->formatDatetime())
            ->toggleable(isToggledHiddenByDefault: true);
    }
}
