<?php

namespace App\Transformers;

use App\Models\UserFlag;

class UserFlagTransformer extends EntityTransformer
{

    protected $defaultIncludes = [];

    /**
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(UserFlag $user_flag)
    {
        return [
            'id' => (int) $user_flag->id,
            'user_id' => (int) $user_flag->user_id,
            'flag_user_id' => (int) $user_flag->flag_user_id,
        ];
    }

}
