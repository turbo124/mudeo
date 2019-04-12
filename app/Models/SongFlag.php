<?php

namespace App\Models;

use App\Models\Song;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class SongFlag extends EntityModel
{

    protected $fillable = [
        'song_id',
        'user_id',
    ];


    public function user()
    {
    	return $this->hasOne(User::class);
    }

    public function song()
    {
    	return $this->hasOne(Song::class, 'id', 'song_id');
    }
    
}
