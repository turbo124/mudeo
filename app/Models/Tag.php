<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends EntityModel
{
    use SoftDeletes;

    public function song()
    {
        return $this->morphedByMany(Song::class, 'taggable');
    }

    public function track()
    {
        return $this->morphedByMany(Track::class, 'taggable');
    }

}
