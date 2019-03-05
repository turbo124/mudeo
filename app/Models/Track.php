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
        $this->morphMany(Comment::class, 'commentable');
    }

    public function tags()
    {
    	$this->morphMany(Tag::clasls, 'taggable');
    }
}
