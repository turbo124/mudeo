<?php

namespace App\Http\Controllers\Requests\Comment;

use App\Http\Requests\Request;
use App\Models\SongComment;
use Illuminate\Support\Facades\Hash;

class CreateCommentRequest extends Request
{
    public function rules()
    {
        $this->sanitize();

        return [
            'song_id' => 'required',
            'description' => 'required',
        ];
    }

    public function sanitize()
    {
        //
    }
}
