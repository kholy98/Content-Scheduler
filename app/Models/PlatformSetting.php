<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'platform_id',
        'characters_limit',
        'allow_images',
        'allow_videos',
    ];

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }
}
