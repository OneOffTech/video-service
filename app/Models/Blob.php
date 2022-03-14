<?php

namespace App\Models;

use App\Enums\BlobRoles;
use Dyrynda\Database\Casts\EfficientUuid;
use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Blob extends Model
{
    use HasFactory;
    use GeneratesUuid;

    protected $casts = [
        'uuid' => EfficientUuid::class,
        'role' => BlobRoles::class,
        'conversions' => AsArrayObject::class,
        'properties' => AsArrayObject::class,
    ];

    protected $fillable = [
        'disk',
        'conversions_disk',
        'name',
        'file_name',
        'mime_type',
        'role',
        'size',
        'width',
        'height',
        'conversions',
        'properties',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'conversions_disk' => 'public',
    ];

    /**
     * Get folder inside the disk that contains the blob.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function diskFolder(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => dirname($attributes['file_name']),
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|\App\Models\Video 
     */
    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVideo($query)
    {
        return $query->where('role', BlobRoles::VIDEO);
    }
    
    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeThumbnail($query)
    {
        return $query->where('role', BlobRoles::THUMBNAIL);
    }
}
