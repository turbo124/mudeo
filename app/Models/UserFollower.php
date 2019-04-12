<?php

namespace App\Models;

class UserFollower extends EntityModel
{

    protected $fillable = [
        'user_following_id',
        'user_id',
    ];


    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function user_following()
    {
    	return $this->belongsTo(User::class, 'user_following_id', 'id');
    }
}