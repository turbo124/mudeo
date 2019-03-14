<?php

namespace App\Filters;

use App\Filters\SongFilters;
use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/**
 * SongFilters
 */
class SongFilters extends QueryFilters
{

 	public function user_id($user_id) : Builder
	{
		return $this->builder->where('user_id', $user_id);
	}

    public function sort(string $sort) : Builder
    {
        $sort_col = explode("|", $sort);
        
        return $this->builder->orderBy($sort_col[0], $sort_col[1]);
    }
}
