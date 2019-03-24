<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;

class CreateUserRequest extends Request
{

    public function rules()
    {
        $this->sanitize();

        return [
            'email' => 'required|unique:users|string|email|max:100',
            'handle' => 'required|without_spaces|unique:users|max:100',
            'password' => 'sometimes|required|string|min:6',
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

        $this->replace($input);     
    }

}