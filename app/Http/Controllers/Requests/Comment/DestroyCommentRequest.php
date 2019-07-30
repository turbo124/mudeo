<?php

namespace App\Http\Controllers\Requests\Comment;

use App\Http\Requests\Request;
use App\Models\SongComment;

class DestroyCommentRequest extends Request
{

    public function authorize()
    {
        return $this->user()->id === SongComment::find($this->song_comment)->user_id;
    }

    public function rules()
    {
       return [];
    }

    public function sanitize()
    {

    }
}
