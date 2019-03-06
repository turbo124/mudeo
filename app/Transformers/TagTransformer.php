<?php

namespace App\Transformers;

use App\Models\Tag;

class TagTransformer extends EntityTransformer
{

    protected $defaultIncludes = [];

    protected $availableIncludes = [
        'songs',
        'tracks',
    ];

    public function transform(Tag $tag)
    {
        return [
            'id' => (int) $tag->id,
            'name' => $tag->name,
            'is_flagged' => (bool) $tag->is_flagged,
            'updated_at' => $tag->updated_at,
            'deleted_at' => $tag->deleted_at,
        ];
    }
}
