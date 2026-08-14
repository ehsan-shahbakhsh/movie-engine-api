<?php

namespace App\Enums;

enum VideoType: string
{
    case Trailer = 'trailer';
    case Teaser = 'teaser';
    case Clip = 'clip';
    case Featurette = 'featurette';
    case Promotional = 'promotional';
}
