<?php

namespace App\Transformers;

use App\Models\SongVideo;

class SongVideoTransformer extends EntityTransformer
{

    protected $defaultIncludes = [
        'video'
    ];

    protected $availableIncludes = [
    ];

    public function transform(SongVideo $song_video)
    {
        return [
            'id' => (int) $song_video->id,
            'song_id' => (int) $song_video->song_id,
            'video_id' => (int) $song_video->video_id,
            'order_id' => (int) $song_video->order_id,
            'volume' => (int) $song_video->volume,
            'delay' => (int) $song_video->delay,
            'updated_at' => $song_video->updated_at,
            'deleted_at' => $song_video->deleted_at,
        ];
    }

     public function includeVideo(SongVideo $song_video)
    {
        $transformer = new VideoTransformer($this->serializer);

        return $this->includeItem($song_video->video, $transformer, 'video');
    }
}
