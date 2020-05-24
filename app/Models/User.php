<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword;

class User extends Authenticatable implements CanResetPassword
{
    use Notifiable;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
        'updated_at',
        'created_at',
        'deleted_at',
        'include',
        'q',
        'api_secret',
        'server_name',
        'token',
        'remember_token',
        'header_image_url',
        'song_likes',
        'song_flags',
        'user_flags',
        'following',
        'is_paid',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'order_id',
        'order_expires',
        'platform',
        'device',
        'oauth_token',
        'token',
        'ip',
        'email',
        'email_verified_at',
        'oauth_user_id',
        'oauth_provider_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function songs()
    {
        return $this->hasMany(Song::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function song_comments()
    {
        return $this->hasMany(SongComment::class);
    }

    public function joinedSongs()
    {
        return $this->belongsToMany('App\Models\Song')->withTimestamps();
    }

    public function song_likes()
    {
        return $this->hasMany(SongLike::class);
    }

    public function followers()
    {
        return $this->hasMany(UserFollower::class, 'user_following_id', 'id');
    }

    public function following()
    {
        return $this->hasMany(UserFollower::class);
    }

    public function video_likes()
    {
        return $this->hasMany(VideoLike::class);
    }

    public function song_flags()
    {
        return $this->hasMany(SongFlag::class);
    }

    public static function admin()
    {
        return static::where('id', '=', 2)->first();
    }

    public function twitterHandle()
    {
        if (! $this->twitter_social_url) {
            return false;
        }

        $parts = explode('/', $this->twitter_social_url);
        $part = $parts[count($parts) - 1];
        $part = ltrim($part, '@');

        $parts = explode('?', $part);
        $part = $parts[0];

        return '@' . $part;
    }

    public function hasPrivateStorage()
    {
        return $this->order_expires && $this->order_expires >= date('Y-m-d');
    }

    public function isAdmin()
    {
        return $this->id == 2;
    }

    public function getProfileImageUrl()
    {
        if (! $this->profile_image_url) {
            return '';
        }

        $url = str_replace('nyc3.digitaloceanspaces', 'nyc3.cdn.digitaloceanspaces', $this->profile_image_url);

        return $url . '?updated_at=' . urlencode($this->updated_at);
    }

    public function getHeaderImageUrl()
    {
        if (! $this->header_image_url) {
            return '';
        }

        $url = str_replace('nyc3.digitaloceanspaces', 'nyc3.cdn.digitaloceanspaces', $this->header_image_url);

        return $url . '?updated_at=' . urlencode($this->updated_at);
    }

}
