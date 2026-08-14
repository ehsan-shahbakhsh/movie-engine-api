<?php

namespace App\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

enum VideoType: string implements HasLabel, HasColor, HasIcon
{
    case Trailer = 'trailer';
    case Teaser = 'teaser';
    case Clip = 'clip';
    case Featurette = 'featurette';
    case Promotional = 'promotional';

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::Trailer => 'تریلر',
            self::Teaser => 'تیزر',
            self::Clip => 'کلیپ',
            self::Featurette => 'محتوای ویژه',
            self::Promotional => 'تبلیغاتی',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Trailer => 'primary',
            self::Teaser => 'info',
            self::Clip => 'success',
            self::Featurette => 'warning',
            self::Promotional => 'gray',
        };
    }

    public function getIcon(): string|BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::Trailer => Heroicon::OutlinedFilm,
            self::Teaser => Heroicon::OutlinedSparkles,
            self::Clip => Heroicon::OutlinedScissors,
            self::Featurette => Heroicon::OutlinedVideoCamera,
            self::Promotional => Heroicon::OutlinedMegaphone,
        };
    }
}
