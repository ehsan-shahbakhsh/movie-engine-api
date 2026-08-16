<?php

namespace App\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

enum SeriesPublishStatus: string implements HasLabel, HasColor, HasIcon
{
    case Draft = 'draft';
    case Published = 'published';
    case ComingSoon = 'coming_soon';
    case Archived = 'archived';

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::Draft => 'پیش‌نویس',
            self::Published => 'منتشر شده',
            self::ComingSoon => 'بزودی',
            self::Archived => 'آرشیو شده',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Published => 'success',
            self::ComingSoon => 'info',
            self::Archived => 'danger',
        };
    }

    public function getIcon(): string|BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::Draft => Heroicon::OutlinedPencilSquare,
            self::Published => Heroicon::OutlinedCheckCircle,
            self::ComingSoon => Heroicon::OutlinedSparkles,
            self::Archived => Heroicon::OutlinedArchiveBox,
        };
    }
}
