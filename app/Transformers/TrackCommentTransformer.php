<?php

namespace App\Transformers;

use App\Models\Track;
use App\Models\TrackComment;

class TrackCommentTransformer extends EntityTransformer
{

    protected $defaultIncludes = [];

    protected $availableIncludes = [
        'track',
    ];

    public function transform(TrackComment $comment)
    {
        return [
            'id' => (int) $comment->id,
            'user_id' => (int) $comment->user_id,
            'track_id' => (int) $comment->track_id,
            'description' => $comment->description ?:'',
            'is_flagged' => (bool) $comment->is_flagged,
            'updated_at' => $comment->updated_at,
            'deleted_at' => $comment->deleted_at,
        ];
    }

    public function includeTrack(TrackComment $comment)
    {
        $transformer = new TrackTransformer($this->serializer);

        return $this->includeItem($comment->track, $transformer, Track::class);
    }
}
