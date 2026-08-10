<?php

namespace App\Enums;

enum MoviePersonDepartment: string
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
}
