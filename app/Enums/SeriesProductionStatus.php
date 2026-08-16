<?php

namespace App\Enums;

enum SeriesProductionStatus: string
{
    case Ongoing = 'ongoing';
    case Ended = 'ended';
    case Canceled = 'canceled';
}
