<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class SongTrack extends Pivot
{

    use SoftDeletes;

    protected $guarded = ['id'];

    public function songs()
    {
        //return $this->hasOne(User::class)->withPivot('permissions', 'settings', 'is_admin', 'is_owner', 'is_locked');
        return $this->hasMany(Song::class);
    }

    public function tracks()
    {
    	return $this->hasMany(Track::class);
    }
}