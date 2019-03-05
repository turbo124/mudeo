<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SongComment extends Model
{
    public function song()
    {
        $this->belongsTo(Song::class);
    }

}
