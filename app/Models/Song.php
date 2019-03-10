<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Song extends EntityModel
{
    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function videos()
    {
        return $this->belongsToMany(Video::class)->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(SongComment::class);
    }

    public function tags()
    {
    	return $this->morphToMany(Tag::class, 'taggable');
    }

    public function song_videos()
    {
        return $this->hasMany(SongVideo::class);
    }

    public function likes()
    {
        return $this->hasMany(SongLike::class);
    }
}
