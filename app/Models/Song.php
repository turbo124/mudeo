<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Song extends Model
{
    use SoftDeletes;

    public function tracks()
    {
        return $this->belongsToMany(Track::class)->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(SongComment::class);
    }

    public function tags()
    {
    	return $this->morphMany(Tag::class, 'taggable');
    }
}
