<?php

namespace App\Transformers;

use App\Models\Song;
use App\Models\SongComment;
use App\Models\Video;
use App\Models\User;

class UserTransformer extends EntityTransformer
{

    protected $defaultIncludes = [];

    protected $availableIncludes = [
        'songs',
        'videos',
        'song_comments',
    ];

    public function transform(User $user)
    {
        return [
            'id' => (int) ($user->id),
            'name' => $user->name ?:'',
            'email' => $user->email ?:'',
            'profile_image_url' => $user->profile_image_url ?: '',
            'header_image_url' => $user->header_image_url ?: '',
            'updated_at' => $user->updated_at,
            'deleted_at' => $user->deleted_at,
            'handle' => $user->handle ?:'',
            'is_flagged' => (bool) $user->is_flagged,
        ];
    }

    public function includeSongs(User $user)
    {
        $transformer = new SongTransformer($this->serializer);

        return $this->includeCollection($user->songs, $transformer, Song::class);
    }

    public function includeVideos(User $user)
    {
        $transformer = new VideoTransformer($this->serializer);

        return $this->includeCollection($user->videos, $transformer, Video::class);
    }

    public function includeSongComments(User $user)
    {
        $transformer = new SongCommentsTransformer($this->serializer);

        return $this->includeCollection($user->song_comments, $transformer, SongComment::class);
    }

    public function includeTrackComments(User $user)
    {
        $transformer = new SongCommentsTransformer($this->serializer);

        return $this->includeCollection($user->track_comments, $transformer, TrackComment::class);
    }
}
