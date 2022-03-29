<?php

namespace App\Models;

use App\Enums\BlobRoles;
use App\Enums\ConversionTargets;
use Dyrynda\Database\Casts\EfficientUuid;
use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

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
     * Get public URL of this blob as stored on the conversions disk.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => Storage::disk($attributes['conversions_disk'])->url($attributes['file_name']),
        );
    }
    
    /**
     * Get public URL of the HLS playlist, if defined, as stored on the conversions disk.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function hlsPlaylistUrl(): Attribute
    {
        return Attribute::make(
            get: function($value, $attributes){
                
                if(!$attributes['conversions']){
                    return null;
                }

                $hlsConversion = $this->castAttribute('conversions', $attributes['conversions'])->collect()->where('type', ConversionTargets::HLS->value)->first();

                if(!$hlsConversion){
                    return null;
                }

                return Storage::disk($attributes['conversions_disk'])->url($hlsConversion['file_name']);
            },
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
        return $query->whereIn('role', [BlobRoles::THUMBNAIL, BlobRoles::THUMBNAIL_STRIP]);
    }
}
