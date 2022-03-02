<?php

namespace App\Models;

use Dyrynda\Database\Casts\EfficientUuid;
use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Blob extends Model
{
    use HasFactory;
    use GeneratesUuid;

    protected $casts = [
        'uuid' => EfficientUuid::class,
    ];

    protected $fillable = [
        'disk',
        'name',
        'file_name',
        'mime_type',
        'size',
        'width',
        'height',
        'conversions',
        'properties',
    ];
}
