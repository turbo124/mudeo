<?php

namespace App\Http\Controllers\Requests\Comment;

use App\Http\Requests\Request;
use App\Models\SongComment;

class DestroyCommentRequest extends Request
{

    public function authorize()
    {
        $comment = SongComment::find($this->song_comment);

        return $this->user()->id === $comment->user_id || $this->user()->id === $comment->song->user_id;
    }

    public function rules()
    {
       return [];
    }

    public function sanitize()
    {

    }
}
