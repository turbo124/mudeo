<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserFlag extends EntityModel
{

    protected $fillable = [
        'flag_user_id',
        'user_id',
    ];


    public function user()
    {
    	return $this->hasOne(User::class);
    }

    public function flag_user()
    {
    	return $this->hasOne(User::class, 'id', 'flag_user_id');
    }
    
}
