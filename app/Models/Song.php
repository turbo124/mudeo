<?php

namespace App\Models;

use App\Models\Filterable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Song extends EntityModel
{
    use Filterable;

        protected $guarded = [
        'id',
        'updated_at',
        'created_at',
        'deleted_at',
        'q',
        'api_secret',
        'videos',
        'song_videos',
    	'isChanged',
    	'user',
    	'include',
    ];

    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function videos()
    {
        return $this->belongsToMany(Video::class)->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(SongComment::class);
    }

    public function tags()
    {
    	return $this->morphToMany(Tag::class, 'taggable');
    }

    public function song_videos()
    {
        return $this->hasMany(SongVideo::class);
    }

    public function song_likes()
    {
        return $this->hasMany(SongLike::class);
    }

    public function parent_song()
    {
       return $this->belongsTo(static::class, 'parent_id');
    }

    public function parent_songs()
    {
       return $this->belongsToMany(static::class, 'parent_id'); //not sure if this will work
    }

    public function child_songs()
    {
       return $this->hasMany(static::class, 'parent_id');
    }
}
