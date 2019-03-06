<?php

namespace App\Transformers;

use App\Models\SongComment;

class SongCommentTransformer extends EntityTransformer
{

    protected $defaultIncludes = [];

    public function transform(SongComment $comment)
    {
        return [
            'id' => (int) $comment->id,
            'user_id' => (int) $comment->user_id,
            'song_id' => (int) $comment->song_id,
            'description' => $comment->description,
            'is_flagged' => (bool) $comment->is_flagged,
            'updated_at' => $comment->updated_at,
            'deleted_at' => $comment->deleted_at,
        ];
    }
}
