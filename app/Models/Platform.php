<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    protected $fillable = [
        'name',
        'type',
    ];

    public function posts() {
        return $this->belongsToMany(Post::class, 'post_platform')
            ->withPivot('platform_status')
            ->withTimestamps();
    }

    public function setting()
    {
        return $this->hasOne(PlatformSetting::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('is_active')
            ->withTimestamps();
    }

}
