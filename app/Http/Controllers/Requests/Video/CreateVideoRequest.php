<?php

namespace App\Http\Requests\Video;

use App\Http\Requests\Request;
use App\Models\Video;
use Illuminate\Support\Facades\Hash;

class CreateVideoRequest extends Request
{

    public function rules()
    {
        $this->sanitize();

        return [
            //'email' => 'required|unique:users|string|email|max:100',
            //'handle' => 'required|unique:users|max:100',
           // 'first_name'        => 'required|string|max:100',
           // 'last_name'         =>  'required|string:max:100',
            //'password'          => 'required|string|min:6',
        ];
    }

    public function sanitize()
    {
        $input = $this->all();
        
        $input['user_id'] = auth()->user()->id;
        
        $this->replace($input);     
    }

}