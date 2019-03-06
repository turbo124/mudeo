<?php

namespace App\Transformers;

use App\Models\Track;

class TrackTransformer extends EntityTransformer
{

    protected $defaultIncludes = [];

    protected $availableIncludes = [
        'songs',
        'comments',
        'tags'
    ];

    public function transform(Track $track)
    {
        return [
            'id' => (int) $track->id,
            'user_id' => (int) $track->user_id,
            'title' => $track->title,
            'url' => $track->url,
            'description' => $track->description,
            'duration' => (int) $track->duration,
            'likes' => (int) $track->likes,
            'is_flagged' => (bool) $track->is_flagged,
            'is_public' => (bool) $track->is_public,
            'updated_at' => $track->updated_at,
            'deleted_at' => $track->deleted_at,
        ];
    }
}
