<?php

namespace App\Http\Controllers\Requests\Song;

use App\Http\Requests\Request;
use App\Models\Song;

class DestroySongRequest extends Request
{

    public function authorize()
    {
        return $this->user()->id === $this->song->user_id;
    }

    public function rules()
    {
       return [];
    }

    public function sanitize()
    {
        
    }

}