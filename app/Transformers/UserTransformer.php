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
            'description' => $user->description ?: '',
            'profile_image_url' => $user->profile_image_url ?: '',
            'header_image_url' => $user->header_image_url ?: '',
            'updated_at' => $user->updated_at,
            'deleted_at' => $user->deleted_at,
            'handle' => $user->handle ?:'',
            'is_flagged' => (bool) $user->is_flagged,
            'facebook_social_url' => $user->facebook_social_url ?: '',
            'youtube_social_url' => $user->youtube_social_url ?: '',
            'instagram_social_url' => $user->instagram_social_url ?: '',
            'soundcloud_social_url' => $user->soundcloud_social_url ?: '',
            'twitch_social_url' => $user->twitch_social_url ?: '',
            'twitter_social_url' => $user->twitter_social_url ?: '',
            'website_social_url' => $user->website_social_url ?: '',
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
