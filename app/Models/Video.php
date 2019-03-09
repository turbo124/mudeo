<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends EntityModel
{
    use SoftDeletes;

    public function songs()
    {
        return $this->belongsToMany(Song::class)->withTimestamps();
    }

    public function tags()
    {
    	return $this->morphToMany(Tag::class, 'taggable');
    }
}
