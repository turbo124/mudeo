<?php

namespace App\Transformers;

use App\Models\SongFlag;

class SongFlagTransformer extends EntityTransformer
{

    protected $defaultIncludes = [];

    /**
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(SongFlag $song_flag)
    {
        return [
            'id' => (int) $song_flag->id,
            'user_id' => (int) $song_flag->user_id,
            'song_id' => (int) $song_flag->song_id,
        ];
    }

}

