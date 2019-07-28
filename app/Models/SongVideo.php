<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class SongVideo extends Pivot
{

    use SoftDeletes;

    protected $fillable = [
        'volume',
        'delay',
        'order_id',
        'song_id',
        'video_id'
    ];

    public function songs()
    {
        return $this->hasMany(Song::class);
    }

    public function song()
    {
        return $this->belongsTo(Song::class);
    }

    public function video()
    {
    	return $this->hasOne(Video::class, 'id', 'video_id');
    }
}
