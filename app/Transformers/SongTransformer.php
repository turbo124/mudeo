<?php

namespace App\Transformers;

use App\Models\Song;
use App\Models\SongComment;
use App\Models\Track;

class SongTransformer extends EntityTransformer
{

    protected $defaultIncludes = [
        'song_videos',
        'comments',
    ];

    protected $availableIncludes = [
        'tags',
        'user'
    ];

    public function transform(Song $song)
    {
        return [
            'id' => (int) $song->id,
            'user_id' => (int) $song->user_id,
            'title' => $song->title ?:'',
            'url' => $song->url ? $song->url : '',
            'description' => $song->description ?:'',
            'duration' => (int) $song->duration,
            'count_like' => (int) $song->count_like,
            'count_play' => (int) $song->count_play,
            'is_flagged' => (bool) $song->is_flagged,
            'is_public' => (bool) $song->is_public,
            'is_approved' => (bool) $song->is_approved,
            'genre_id' => (int) $song->genre_id,
            'parent_id' => (int) $song->parent_id,
            'updated_at' => $song->updated_at,
            'deleted_at' => $song->deleted_at,
            'video_url' => $song->video_url ? $song->video_url : '',
            'thumbnail_url' => $song->thumbnail_url ? $song->thumbnail_url : '',
            'count_like' => (int) $song->count_like,
            'layout' => $song->layout,
            'is_rendered' => (bool) $song->is_rendered,
        ];
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

    public function includeSongVideos(Song $song)
    {
        $transformer = new SongVideoTransformer($this->serializer);

        return $this->includeCollection($song->song_videos, $transformer, SongVideo::class);
    }

    public function includeUser(Song $song)
    {
        $transformer = new UserTransformer($this->serializer);

        return $this->includeItem($song->user, $transformer, User::class);
    }
}
