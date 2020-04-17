<?php

namespace App\Models;

use App\Models\Filterable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Song extends EntityModel
{
    use Notifiable;
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
        'comments',
        'url',
        'video_url',
        'thumbnail_url',
        'youtube_id',
        'youtube_published_id',
        'is_featured',
        'is_approved',
        'is_public',
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
        return $this->hasMany(SongVideo::class)->orderBy('order_id');
    }

    public function local_song_videos()
    {
        return $this->hasMany(SongVideo::class)->local()->orderBy('order_id');
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

    public function youTubeThumbnailUrl()
    {
        return 'https://img.youtube.com/vi/' . $this->youtube_id . '/hqdefault.jpg';
    }

    public function youTubeEmbedUrl()
    {
        return 'https://www.youtube.com/embed/'. $this->youtube_id . '?autoplay=1&modestbranding=1&rel=0';
    }

    public function genre()
    {
        if (! $this->genre_id) {
            return '';
        }

        $map = [
            1 => 'African',
            2 => 'Arabic',
            3 => 'Asian',
            4 => 'Avant Garde',
            5 => 'Blues',
            6 => 'Caribbean',
            7 => 'Classical Music',
            8 => 'Comedy',
            9 => 'Country',
            10 => 'Easy Listening',
            11 => 'Electronic',
            12 => 'Folk',
            13 => 'Hip Hop',
            14 => 'Jazz',
            15 => 'Latin',
            16 => 'Pop',
            17 => 'Soul',
            18 => 'Rock',
            19 => 'Other',
        ];

        return $map[$this->genre_id];
    }
}
