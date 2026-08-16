<?php

namespace App\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

enum SeriesProductionStatus: string implements HasLabel, HasColor, HasIcon
{
    case Ongoing = 'ongoing';
    case Ended = 'ended';
    case Canceled = 'canceled';

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::Ongoing => 'در حال پخش',
            self::Ended => 'پایان یافته',
            self::Canceled => 'کنسل شده',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Ongoing => 'info',
            self::Ended => 'success',
            self::Canceled => 'danger',
        };
    }

    public function getIcon(): string|BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::Ongoing => Heroicon::OutlinedPlay,
            self::Ended => Heroicon::OutlinedCheckCircle,
            self::Canceled => Heroicon::OutlinedXCircle,
        };
    }
}
