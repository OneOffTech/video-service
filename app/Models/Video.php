<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Casts\EfficientUuid;
use Dyrynda\Database\Support\BindsOnUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Video extends Model
{
    use HasFactory;
    use GeneratesUuid;
    use BindsOnUuid;

    protected $casts = [
        'uuid' => EfficientUuid::class,
    ];

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'language',
        'tags',
        'license',
    ];
    
    public function blobs()
    {
        return $this->hasMany(Blob::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
