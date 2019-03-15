<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;

class UpdateUserRequest extends Request
{

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


        $this->replace($input);     
    }

}