<?php

namespace App\Enums;

enum SeriesPublishStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case ComingSoon = 'coming_soon';
    case Archived = 'archived';
}
