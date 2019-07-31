<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class SongComment extends EntityModel
{
	use SoftDeletes;

	protected $guarded = [
        'id',
		'user_id',
        'updated_at',
        'created_at',
        'deleted_at',
		'user',
    ];

	public function song()
    {
        return $this->belongsTo(Song::class);
    }

	public function user()
    {
        return $this->belongsTo(User::class);
    }

}
