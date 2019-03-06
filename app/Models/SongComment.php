<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SongComment extends EntityModel
{

	use SoftDeletes;

    public function song()
    {
        return $this->belongsTo(Song::class);
    }

}
