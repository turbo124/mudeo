<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
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
