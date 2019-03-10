<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class SongLike extends EntityModel
{

    
    public function video()
    {
        return $this->hasOne(Video::class);
    }

    public function user()
    {
    	return $this->hasOne(User::class);
    }
    
}
