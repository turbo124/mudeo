<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    
    public function songs()
    {
        return $this->morphedByMany(Song::class, 'taggable');
    }

    public function tracks()
    {
        return $this->morphedByMany(Track::class, 'taggable');
    }

}
