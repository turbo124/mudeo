<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;
use App\Models\Client;

class CreateUserRequest extends Request
{

    public function rules()
    {
        return [
            'email' => 'required|unique:users|string|email|max:100',
           // 'first_name'        => 'required|string|max:100',
           // 'last_name'         =>  'required|string:max:100',
            'password'          => 'required|string|min:6',
        ];
    }

}