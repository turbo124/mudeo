<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends EntityModel
{
    use SoftDeletes;

    protected $guarded = [
        'id',
        'updated_at',
        'created_at',
        'deleted_at',
        'q',
        'api_secret',
        'song_id',
        'video',
        'description',
        'duration',
    ];

    public function songs()
    {
        return $this->belongsToMany(Song::class)->withTimestamps();
    }

    public function song_videos()
    {
        return $this->belongsToMany(SongVideo::class)->withTimestamps();
    }

    public function tags()
    {
    	return $this->morphToMany(Tag::class, 'taggable');
    }

    public function video_likes()
    {
        return $this->hasMany(VideoLike::class);
    }

    public function getUrl()
    {
        if (! $this->url) {
            return '';
        }

        return str_replace('nyc3.digitaloceanspaces', 'nyc3.cdn.digitaloceanspaces', $this->url);
    }

    public function getThumbnailUrl()
    {
        if (! $this->thumbnail_url) {
            return '';
        }

        return str_replace('nyc3.digitaloceanspaces', 'nyc3.cdn.digitaloceanspaces', $this->thumbnail_url);        
    }
}
