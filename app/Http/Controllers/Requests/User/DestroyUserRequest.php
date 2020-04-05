<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;
use App\Models\User;

class DestroyUserRequest extends Request
{

    public function authorize()
    {
        return auth()->user()->id === $this->user->id;
    }

    public function rules()
    {
       return [];
    }

    public function sanitize()
    {

    }

}
