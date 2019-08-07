<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

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

    public function scopeLocal (Builder $query) {
        return $query->whereHas('video', function ($q) {
                $q->where('remote_video_id', '=', '');
        });
    }}
