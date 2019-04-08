<?php

namespace App\Transformers;

use App\Models\User;
use App\Models\UserFollower;
use App\Transformers\UserTransformer;

class UserFollowerTransformer extends EntityTransformer
{

    protected $defaultIncludes = [];

    /**
     * @var array
     */
    protected $availableIncludes = [
        'user',
    ];

    public function transform(UserFollower $user_follower)
    {
        return [
            'id' => (int) $user_follower->id,
            'user_id' => (int) $user_follower->user_id,
            'user_following_id' => (int) $user_follower->user_following_id,
        ];
    }

    public function includeUser(UserFollower $user_follower)
    {
        $transformer = new UserTransformer($this->serializer);

        return $this->includeItem($user_follower->user, $transformer, User::class);
    }
    }

}
