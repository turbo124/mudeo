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
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'updated_at' => $user->updated_at,
            'deleted_at' => $user->deleted_at,
            'handle' => $user->handle,
            'token' => $user->token,
            'confirmed' => (bool) $user->confirmed,
            'oauth_user_id' => $user->oauth_user_id,
            'oauth_provider_id' => $user->oauth_provider_id,
            'is_flagged' => (bool) $user->is_flagged,
        ];
    }

}
