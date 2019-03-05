<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    public function tracks()
    {
        $this->morphMany(Track::class, 'trackable');
    }

    public function comments()
    {
        $this->morphMany(Comment::class, 'commentable');
    }

    public function tags()
    {
    	$this->morphMany(Tag::clasls, 'taggable');
    }
}
