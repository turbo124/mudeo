<?php

namespace App\Transformers;

use App\Models\Song;

class SongTransformer extends EntityTransformer
{

    protected $defaultIncludes = [];

    protected $availableIncludes = [
        'tracks',
    ];

    public function transform(Song $song)
    {
        return [
            'id' => (int) $song->id,
            'user_id' => (int) $song->user_id,
            'title' => $song->title,
            'url' => $song->url,
            'description' => $song->description,
            'duration' => (int) $song->duration,
            'likes' => (int) $song->likes,
            'is_flagged' => (bool) $song->is_flagged,
            'is_public' => (bool) $song->is_public,
            'updated_at' => $song->updated_at,
            'deleted_at' => $song->deleted_at,
        ];
    }
}
