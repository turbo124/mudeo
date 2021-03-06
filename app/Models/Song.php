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
        'user_id',
        'updated_at',
        'created_at',
        'deleted_at',
        'q',
        'api_secret',
        'videos',
        'song_videos',
        'joined_users',
    	'isChanged',
    	'user',
    	'include',
        'comments',
        'url',
        'video_url',
        'track_video_url',
        'thumbnail_url',
        'youtube_id',
        'youtube_published_id',
        'is_featured',
        'is_rendered',
        'is_approved',
        'is_public',
        'approved_at',
        'approved_at',
        'needs_render',
        'twitter_id',
        'color',
        'width',
        'height',
        'blurhash',
        'sharing_key',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'sharing_key',
    ];

    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function joined_users()
    {
        return $this->belongsToMany('App\Models\User')->withTimestamps();
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

        if (config('mudeo.is_dance')) {
            $map = [
                1 => 'Ballet',
                2 => 'Tap',
                3 => 'Jazz',
                4 => 'Modern',
                5 => 'Lyrical',
                6 => 'Hip Hop',
                7 => 'Contemporary',
                8 => 'Other',
            ];
        } else {
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
        }

        return $map[$this->genre_id];
    }

    public function getVideoUrl()
    {
        if (! $this->video_url) {
            return '';
        }

        $url = str_replace('nyc3.digitaloceanspaces', 'nyc3.cdn.digitaloceanspaces', $this->video_url);

        return $url . '?updated_at=' . urlencode($this->updated_at);
    }

    public function getTrackVideoUrl()
    {
        if (! $this->track_video_url) {
            return '';
        }

        $url = str_replace('nyc3.digitaloceanspaces', 'nyc3.cdn.digitaloceanspaces', $this->track_video_url);

        return $url . '?updated_at=' . urlencode($this->updated_at);
    }

    public function getThumbnailUrl()
    {
        if (! $this->thumbnail_url) {
            return '';
        }

        $url = str_replace('nyc3.digitaloceanspaces', 'nyc3.cdn.digitaloceanspaces', $this->thumbnail_url);

        return $url . '?updated_at=' . urlencode($this->updated_at);
    }
}
