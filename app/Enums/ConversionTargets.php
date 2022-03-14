<?php

namespace App\Enums;

use Dyrynda\Database\Casts\EfficientUuid;
use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

enum ConversionTargets: int
{
    case RESPONSIVE_IMAGE = 10;
    
    case WEBM = 20;

    case HLS = 30;

}
