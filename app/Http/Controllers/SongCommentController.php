<?php

namespace App\Http\Controllers;

use App\Models\SongComment;
use Illuminate\Http\Request;
use App\Http\Controllers\Requests\Comment\CreateCommentRequest;
use App\Http\Controllers\Requests\Comment\DestroyCommentRequest;
use App\Transformers\SongCommentTransformer;
use Hashids\Hashids;
use Illuminate\Support\Facades\Log;

class SongCommentController extends BaseController
{

    protected $entityType = SongComment::class;
    protected $entityTransformer = SongCommentTransformer::class;


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     public function store(CreateCommentRequest $request)
     {
         $comment = new SongComment;
         $comment->fill($request->all());
         $comment->user_id = auth()->user()->id;
         $comment->save();

         return $this->itemResponse($comment->fresh());
     }

    /**
     * Display the specified resource.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(SongComment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function edit(SongComment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SongComment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyCommentRequest $request, SongComment $comment)
    {
        $comment = SongComment::findOrFail($request->song_comment);
        $comment->delete();

        return $this->itemResponse($comment);
    }
}
