<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;

class UpdateUserRequest extends Request
{

    public function authorize()
    {
        return auth()->user()->id === $this->user->id;
    }

    public function rules()
    {
        $this->sanitize();

        return [
        ];
    }

    public function sanitize()
    {
        $input = $this->all();


        if(isset($input['password']))   
            unset($input['password']);

        $input['facebook_social_url'] = isset($input['facebook_social_url']) ? $input['facebook_social_url'] : '';
        $input['youtube_social_url'] = isset($input['youtube_social_url']) ? $input['youtube_social_url'] : '';
        $input['instagram_social_url'] = isset($input['instagram_social_url']) ? $input['instagram_social_url'] : '';
        $input['soundcloud_social_url'] = isset($input['soundcloud_social_url']) ? $input['soundcloud_social_url'] : '';
        $input['twitch_social_url'] = isset($input['twitch_social_url']) ? $input['twitch_social_url'] : '';
        $input['twitter_social_url'] = isset($input['twitter_social_url']) ? $input['twitter_social_url'] : '';
        $input['website_social_url'] = isset($input['website_social_url']) ? $input['website_social_url'] : '';
        $input['description'] = isset($input['description']) ? $input['description'] : '';

        $this->replace($input);     
    }

}