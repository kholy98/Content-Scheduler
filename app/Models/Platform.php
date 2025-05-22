<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    protected $fillable = [
        'name',
        'description',
        'url',
        'icon',
    ];

    public function posts() {
        return $this->belongsToMany(Post::class)
            ->withPivot('platform_status')
            ->withTimestamps();
    }
}
