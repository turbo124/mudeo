<?php

namespace App\Models;

class VideoLike extends EntityModel
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
