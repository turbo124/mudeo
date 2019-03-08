<?php

namespace App\Transformers;

use App\Models\Song;
use App\Models\SongComment;
use App\Models\Track;

class SongTransformer extends EntityTransformer
{

    protected $defaultIncludes = [
        'tracks'
    ];

    protected $availableIncludes = [
        'comments',
        'tags',
    ];

    public function transform(Song $song)
    {
        return [
            'id' => (int) $song->id,
            'user_id' => (int) $song->user_id,
            'title' => $song->title ?:'',
            'url' => $song->url ?:'',
            'description' => $song->description ?:'',
            'duration' => (int) $song->duration,
            'likes' => (int) $song->likes,
            'is_flagged' => (bool) $song->is_flagged,
            'is_public' => (bool) $song->is_public,
            'updated_at' => $song->updated_at,
            'deleted_at' => $song->deleted_at,
        ];
    }

    public function includeTracks(Song $song)
    {
        $transformer = new TrackTransformer($this->serializer);

        return $this->includeCollection($song->tracks, $transformer, 'tracks');
    }

    public function includeComments(Song $song)
    {
        $transformer = new SongCommentTransformer($this->serializer);

        return $this->includeCollection($song->comments, $transformer, SongComment::class);
    }

    public function includeTags(Song $song)
    {
        $transformer = new TagTransformer($this->serializer);

        return $this->includeCollection($song->tags, $transformer, Tag::class);
    }
}
