<?php

namespace App\Transformers;

use App\Models\Song;
use App\Models\SongComment;
use App\Models\SongFlag;
use App\Models\SongLike;
use App\Models\User;
use App\Models\UserFollower;
use App\Models\Video;

class UserAccountTransformer extends EntityTransformer
{

    protected $defaultIncludes = [];

    protected $availableIncludes = [
        'songs',
        'videos',
        'song_comments',
        'song_likes',
        'followers',
        'following',
        'song_flags',
    ];

    public function transform(User $user)
    {
        return [
            'id' => (int) ($user->id),
            'name' => $user->name ?:'',
            'email' => $user->email ?:'',
            'description' => $user->description ?: '',
            'profile_image_url' => $user->profile_image_url ?: '',
            'header_image_url' => $user->header_image_url ?: '',
            'facebook_social_url' => $user->facebook_social_url ?: '',
            'youtube_social_url' => $user->youtube_social_url ?: '',
            'instagram_social_url' => $user->instagram_social_url ?: '',
            'soundcloud_social_url' => $user->soundcloud_social_url ?: '',
            'twitch_social_url' => $user->twitch_social_url ?: '',
            'twitter_social_url' => $user->twitter_social_url ?: '',
            'website_social_url' => $user->website_social_url ?: '',
            'updated_at' => $user->updated_at,
            'deleted_at' => $user->deleted_at,
            'handle' => $user->handle ?:'',
            'token' => $user->token,
            'confirmed' => (bool) $user->confirmed,
            'is_flagged' => (bool) $user->is_flagged,
        ];
    }

    public function includeFollowing(User $user)
    {

        $transformer = new UserFollowerTransformer($this->serializer);

        return $this->includeCollection($user->following, $transformer, UserFollower::class);

    }

    public function includeFollowers(User $user)
    {
        $transformer = new UserFollowerTransformer($this->serializer);

        return $this->includeCollection($user->followers, $transformer, UserFollower::class);
    }

    public function includeSongFlags(User $user)
    {
        $transformer = new SongFlagTransformer($this->serializer);

        return $this->includeCollection($user->song_flags, $transformer, SongFlag::class);

    }

    public function includeSongLikes(User $user)
    {
        $transformer = new SongLikeTransformer($this->serializer);

        return $this->includeCollection($user->song_likes, $transformer, SongLike::class);

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
