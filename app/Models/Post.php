<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'body',
        'user_id',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function platforms() {
        return $this->belongsToMany(Platform::class)
            ->withPivot('platform_status')
            ->withTimestamps();
    }
}
