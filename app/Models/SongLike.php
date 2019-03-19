<?php

namespace App\Models;

use App\Models\Song;
use Illuminate\Database\Eloquent\SoftDeletes;

class SongLike extends EntityModel
{

    protected $fillable = [
        'song_id',
        'user_id',
    ];

    public function video()
    {
        return $this->hasOne(Video::class);
    }

    public function user()
    {
    	return $this->hasOne(User::class);
    }

    public function song()
    {
    	return $this->hasOne(Song::class);
    }
    
}
