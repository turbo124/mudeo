<?php

namespace App\Transformers;

use App\Models\Song;
use App\Models\SongComment;
use App\Transformers\SongTransformer;
use App\Transformers\UserTransformer;

class SongCommentTransformer extends EntityTransformer
{

    protected $defaultIncludes = [
        'user',
    ];

    /**
     * @var array
     */
    protected $availableIncludes = [
        'song',
    ];

    public function transform(SongComment $comment)
    {
        return [
            'id' => (int) $comment->id,
            'user_id' => (int) $comment->user_id,
            'song_id' => (int) $comment->song_id,
            'description' => $comment->description ?:'',
            'is_flagged' => (bool) $comment->is_flagged,
            'updated_at' => $comment->updated_at,
            'deleted_at' => $comment->deleted_at,
        ];
    }

    public function includeSong(SongComment $comment)
    {
        $transformer = new SongTransformer($this->serializer);
        return $this->includeItem($comment->song, $transformer, class_basename(Song::class));
    }

    public function includeUser(SongComment $comment)
    {
        $transformer = new UserTransformer($this->serializer);
        return $this->includeItem($comment->user, $transformer, class_basename(User::class));
    }
}
