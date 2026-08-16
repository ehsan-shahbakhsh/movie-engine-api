<?php

namespace App\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

enum PersonSeriesDepartment: string implements HasLabel, HasColor, HasIcon
{
    case Acting = 'acting';
    case Directing = 'directing';
    case Writing = 'writing';
    case Production = 'production';
    case Camera = 'camera';
    case Editing = 'editing';
    case Sound = 'sound';
    case Art = 'art';
    case VisualEffects = 'visual_effects';
    case Music = 'music';

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::Acting => 'بازیگری',
            self::Directing => 'کارگردانی',
            self::Writing => 'نویسندگی',
            self::Production => 'تهیه‌کنندگی و تولید',
            self::Camera => 'فیلم‌برداری',
            self::Editing => 'تدوین',
            self::Sound => 'صدا',
            self::Art => 'طراحی صحنه و هنر',
            self::VisualEffects => 'جلوه‌های بصری',
            self::Music => 'موسیقی',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Editing, self::Acting => 'danger',
            self::Sound, self::Directing => 'gray',
            self::Art, self::Writing => 'info',
            self::VisualEffects, self::Production => 'warning',
            self::Music, self::Camera => 'success',
        };
    }

    public function getIcon(): string|BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::Acting => Heroicon::OutlinedUserGroup,
            self::Directing => Heroicon::OutlinedVideoCamera,
            self::Writing => Heroicon::OutlinedPencilSquare,
            self::Production => Heroicon::OutlinedBriefcase,
            self::Camera => Heroicon::OutlinedCamera,
            self::Editing => Heroicon::OutlinedScissors,
            self::Sound => Heroicon::OutlinedSpeakerWave,
            self::Art => Heroicon::OutlinedPaintBrush,
            self::VisualEffects => Heroicon::OutlinedSparkles,
            self::Music => Heroicon::OutlinedMusicalNote,
        };
    }
}
