<?php

namespace App\Filament\Shared;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Support\Icons\Heroicon;

final class FormComponents
{
    public static function name(string $name = 'name', string $label = 'نام', bool $required = true): TextInput
    {
        return TextInput::make($name)
            ->required($required)
            ->maxLength(255)
            ->label($label);
    }

    public static function status(string $name = 'is_active', string $label = 'وضعیت'): Toggle
    {
        return Toggle::make($name)
            ->label($label)
            ->default(true)
            ->required()
            ->onColor('success')
            ->offColor('danger')
            ->onIcon(Heroicon::Check)
            ->offIcon(Heroicon::XMark);
    }

    public static function slug(): TextInput
    {
        return TextInput::make('slug')
            ->maxLength(255)
            ->label('نامک (اسلاگ)')
            ->unique(ignoreRecord: true)
            ->regex('/^[a-z0-9\-\_]+$/')
            ->validationMessages(['regex' => 'نامک فقط می‌تواند شامل حروف کوچک انگلیسی، اعداد، خط فاصله (-) و زیرخط (_) باشد.'])
            ->hintIcon(Heroicon::QuestionMarkCircle, 'نامک همان متنی است که در انتهای آدرس مرورگر نمایش داده می‌شود.')
            ->prefixIcon(Heroicon::Link)
            ->helperText('در صورت خالی ماندن، خودکار تولید می‌شود');
    }

    public static function sortOrder(): TextInput
    {
        return TextInput::make('sort_order')
            ->required()
            ->label('ترتیب نمایش')
            ->numeric()
            ->integer()
            ->default(0)
            ->maxValue(9999);
    }
}
