<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    use HasApiTokens;
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
        'password',
        'primary_contact',
        'q',
        'company',
        'country',
        'shipping_country'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
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

    public function tracks()
    {
        return $this->hasMany(Track::class);
    }

    public function song_comments()
    {
        return $this->hasMany(SongComment::class);
    }


    public function track_comments()
    {
        return $this->hasMany(TrackComment::class);
    }
}