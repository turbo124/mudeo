<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    public function songs()
    {
        return $this->morphedByMany(Song::class, 'trackable');
    }

    public function comments()
    {
        return $this->hasMany(TrackComment::class);
    }

    public function tags()
    {
    	return $this->morphMany(Tag::class, 'taggable');
    }
}
