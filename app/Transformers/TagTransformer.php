<?php

namespace App\Transformers;

use App\Models\Song;
use App\Models\Tag;
use App\Transformers\SongTransformer;

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

    public function includeTracks(Tag $tag)
    {
        $transformer = new TrackTransformer($this->serializer);

        return $this->includeCollection($tag->tracks, $transformer, Track::class);
    }

    public function includeSongs(Song $song)
    {
        $transformer = new SongTransformer($this->serializer);

        return $this->includeCollection($tag->songs, $transformer, Song::class);
    }
}
