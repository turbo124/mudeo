<?php

namespace App\Ninja\Transformers;

use App\Models\User;

class UserTransformer extends EntityTransformer
{

    protected $defaultIncludes = [];

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
            'confirmed' => (bool) $user->confirmed,
            'oauth_user_id' => $user->oauth_user_id,
            'oauth_provider_id' => $user->oauth_provider_id,
            'is_flagged' => (bool) $user->is_flagged,
        ];
    }
}
