<?php

namespace App\Enums;

use Dyrynda\Database\Casts\EfficientUuid;
use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

enum BlobRoles: int
{
    case VIDEO = 10;
    
    case THUMBNAIL = 20;
    
    case THUMBNAIL_STRIP = 21;

    case SUBTITLES = 30;
}
