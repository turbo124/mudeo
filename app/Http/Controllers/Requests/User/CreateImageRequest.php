<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;

class CreateImageRequest extends Request
{

    public function rules()
    {
        $this->sanitize();

        return [
            'image' => 'file'
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

       

        $this->replace($input);     
    }

}