<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Track extends EntityModel
{
    use SoftDeletes;

    public function songs()
    {
        return $this->belongsToMany(Song::class)->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(TrackComment::class);
    }

    public function tags()
    {
    	return $this->morphToMany(Tag::class, 'taggable');
    }
}
