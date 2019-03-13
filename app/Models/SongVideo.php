<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class SongVideo extends Pivot
{

    use SoftDeletes;

    protected $fillable = [
        'volume',
        'order_id',
        'song_id',
        'video_id'
    ];

    public function songs()
    {
        //return $this->hasOne(User::class)->withPivot('permissions', 'settings', 'is_admin', 'is_owner', 'is_locked');
        return $this->hasMany(Song::class);
    }

    public function video()
    {
    	return $this->hasOne(Video::class, 'id', 'video_id');
    }
}
