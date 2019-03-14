<?php

namespace App\Transformers;

use App\Models\Song;
use App\Models\SongComment;
use App\Models\Track;
use App\Models\TrackComment;
use App\Models\User;

class UserAccountTransformer extends EntityTransformer
{

    protected $defaultIncludes = [];

    protected $availableIncludes = [];

    public function transform(User $user)
    {
        return [
            'id' => (int) ($user->id),
            'name' => $user->name ?:'',
            'email' => $user->email ?:'',
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
            'oauth_user_id' => $user->oauth_user_id,
            'oauth_provider_id' => $user->oauth_provider_id,
            'is_flagged' => (bool) $user->is_flagged,
        ];
    }

}
