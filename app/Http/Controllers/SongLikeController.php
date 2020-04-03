<?php

namespace App\Http\Controllers;

use App\Models\SongLike;
use App\Transformers\SongLikeTransformer;
use Illuminate\Http\Request;

class SongLikeController extends BaseController
{


    protected $entityType = SongLike::class;
    protected $entityTransformer = SongLikeTransformer::class;

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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
          $song_like = SongLike::firstOrCreate(['song_id' => $request->song_id,
                'user_id' => auth()->user()->id]);

            return $this->itemResponse($song_like);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $song_like = SongLike::where([
                'song_id' => $id,
                'user_id' => auth()->user()->id])->first();

        if($song_like)
            $song_like->delete();

            return response()->json([], 200);
    }




}
