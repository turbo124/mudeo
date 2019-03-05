<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public function songs()
    {
        return $this->morphedByMany(Song::class, 'commentable');
    }

    public function tracks()
    {
        return $this->morphedByMany(Track::class, 'commentable');
    }

    
}
