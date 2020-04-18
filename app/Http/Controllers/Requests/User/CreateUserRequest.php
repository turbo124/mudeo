<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use App\Rules\ExternalLink;

class CreateUserRequest extends Request
{

    public function rules()
    {
        $this->sanitize();

        return [
            'email' => 'required|unique:users|string|email|max:100',
            'handle' => 'required|alpha_dash|unique:users|max:100',
            'password' => 'sometimes|required|string|min:6',
            'twitter_social_url' => [new ExternalLink('https://twitter.com/')],
            'facebook_social_url' => [new ExternalLink('https://www.facebook.com/')],
            'youtube_social_url' => [new ExternalLink('https://www.youtube.com/')],
            'instagram_social_url' => [new ExternalLink('https://www.instagram.com/')],
            'soundcloud_social_url' => [new ExternalLink('https://soundcloud.com/')],
            'twitch_social_url' => [new ExternalLink('https://www.twitch.tv/')],
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        $input['name'] = isset($input['name']) ? $input['name'] : ' ';

        if(isset($input['oauth_user_id']))
            $input['password'] = sha1( time() );

        if(isset($input['password']))
            $input['password'] = Hash::make($input['password']);

        $input['ip'] = request()->ip();

        $input['facebook_social_url'] = isset($input['facebook_social_url']) ? $input['facebook_social_url'] : '';
        $input['youtube_social_url'] = isset($input['youtube_social_url']) ? $input['youtube_social_url'] : '';
        $input['instagram_social_url'] = isset($input['instagram_social_url']) ? $input['instagram_social_url'] : '';
        $input['soundcloud_social_url'] = isset($input['soundcloud_social_url']) ? $input['soundcloud_social_url'] : '';
        $input['twitch_social_url'] = isset($input['twitch_social_url']) ? $input['twitch_social_url'] : '';
        $input['twitter_social_url'] = isset($input['twitter_social_url']) ? $input['twitter_social_url'] : '';
        $input['website_social_url'] = isset($input['website_social_url']) ? $input['website_social_url'] : '';
        $input['description'] = isset($input['description']) ? $input['description'] : '';
        $input['profile_image_url'] = isset($input['profile_image_url']) ? $input['profile_image_url'] : '';
        $input['follower_count'] = 0;

        $this->replace($input);
    }

}
