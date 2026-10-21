<?php

namespace App\Enums;


enum BlobRoles: int
{
    case VIDEO = 10;
    
    case THUMBNAIL = 20;
    
    case THUMBNAIL_STRIP = 21;

    case SUBTITLES = 30;
}
