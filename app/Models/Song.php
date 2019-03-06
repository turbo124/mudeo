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
    	return $this->morphToMany(Tag::class, 'taggable');
    }
}
