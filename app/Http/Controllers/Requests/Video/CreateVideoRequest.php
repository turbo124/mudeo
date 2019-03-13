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
            'video' => 'file'
        ];
    }

    public function sanitize()
    {
        $input = $this->all();
        
        $input['user_id'] = auth()->user()->id;
        $this->replace($input);     
    }

}