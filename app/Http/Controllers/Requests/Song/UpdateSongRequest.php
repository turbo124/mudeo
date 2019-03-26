<?php

namespace App\Http\Controllers\Requests\Song;

use App\Http\Requests\Request;
use App\Models\Song;
use Illuminate\Support\Facades\Hash;

class UpdateSongRequest extends Request
{
/*
    public function authorize()
    {
        return $this->user()->id === $this->song->user_id;
    }
  */  
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
        $input['description'] = isset($input['description']) ? $input['description'] : ' ';
        $input['title'] = isset($input['title']) ? $input['title'] : ' ';
        $input['video_url'] = isset($input['video_url']) ? $input['video_url'] : ' ';

        $this->replace($input);     
    }

}