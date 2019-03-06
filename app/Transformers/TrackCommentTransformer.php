<?php

namespace App\Transformers;

use App\Models\TrackComment;

class TrackCommentTransformer extends EntityTransformer
{

    protected $defaultIncludes = [];

    public function transform(TrackComment $comment)
    {
        return [
            'id' => (int) $comment->id,
            'user_id' => (int) $comment->user_id,
            'track_id' => (int) $comment->track_id,
            'description' => $comment->description,
            'is_flagged' => (bool) $comment->is_flagged,
            'updated_at' => $comment->updated_at,
            'deleted_at' => $comment->deleted_at,
        ];
    }
}
