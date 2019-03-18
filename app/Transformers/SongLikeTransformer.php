<?php

namespace App\Transformers;

use App\Models\Song;
use App\Models\SongComment;
use App\Models\SongLike;
use App\Transformers\SongTransformer;

class SongLikeTransformer extends EntityTransformer
{

    protected $defaultIncludes = [];

    /**
     * @var array
     */
    protected $availableIncludes = [
    ];

    public function transform(SongLike $like)
    {
        return [
            'id' => (int) $like->id,
            'user_id' => (int) $like->user_id,
            'song_id' => (int) $like->song_id,
        ];
    }

}
