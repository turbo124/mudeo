<?php

namespace App\Transformers;

use App\Models\Song;
use App\Models\Tag;
use App\Models\Video;

class VideoTransformer extends EntityTransformer
{

    protected $defaultIncludes = [];

    protected $availableIncludes = [
        'songs',
        'comments',
        'tags'
    ];

    public function transform(Video $video)
    {
        return [
            'id' => (int) $video->id,
            'user_id' => (int) $video->user_id,
            'title' => $video->title ?: '',
            'url' => $video->url ?:'',
            'thumbnail_url' => $video->thumbnail_url ?:'',
            'description' => $video->description ?: '',
            'duration' => (int) $video->duration,
            'likes' => (int) $video->likes,
            'is_flagged' => (bool) $video->is_flagged,
            'is_public' => (bool) $video->is_public,
            'updated_at' => $video->updated_at,
            'deleted_at' => $video->deleted_at,
        ];
    }

    public function includeSongs(Video $video)
    {
        $transformer = new SongTransformer($this->serializer);

        return $this->includeCollection($video->songs, $transformer, Song::class);
    }


    public function includeTags(Video $video)
    {
        $transformer = new TagTransformer($this->serializer);

        return $this->includeCollection($video->tags, $transformer, Tag::class);
    }
}
