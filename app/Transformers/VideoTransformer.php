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
            'url' => $video->getUrl(),
            'thumbnail_url' => $video->getThumbnailUrl(),
            'description' => $video->description ?: '',
            'duration' => (int) $video->duration,
            'is_flagged' => (bool) $video->is_flagged,
            'is_public' => (bool) $video->is_public,
            'updated_at' => $video->updated_at,
            'deleted_at' => $video->deleted_at,
            'remote_video_id' => $video->remote_video_id,
            'volume_data' => $video->volume_data ? json_decode($video->volume_data) : new \stdClass,
            'max_volume' => $video->max_volume,
            'recognitions' => $videl->recognitions,
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
