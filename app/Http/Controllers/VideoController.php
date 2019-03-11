<?php

namespace App\Http\Controllers;

use App\Http\Requests\Video\CreateVideoRequest;
use App\Models\Song;
use App\Models\Video;
use App\Transformers\VideoTransformer;
use Illuminate\Http\Request;

class VideoController extends BaseController
{

    protected $entityType = Video::class;
    protected $entityTransformer = VideoTransformer::class;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $videos = Video::orderBy('updated_at', 'desc');
        
        return $this->listResponse($videos);
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
    public function store(CreateVideoRequest $request)
    {
        
        $video = Video::create($request->all());
        
        $video->save();

        if($request->input('song_id')) {

            $song = Song::find($request->input('song_id'))->first();

            $song->videos()->sync($video);

        }

        if($request->file('video'))
            $request->file('video')->store('videos');

        return $this->itemResponse($video);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Track  $track
     * @return \Illuminate\Http\Response
     */
    public function show(Video $video)
    {
    return $this->itemResponse($video);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Track  $track
     * @return \Illuminate\Http\Response
     */
    public function edit(Video $video)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Track  $track
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Video $video)
    {
        $video->file($request->all());
        $video->save();

        return $this->itemResponse($video);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Track  $track
     * @return \Illuminate\Http\Response
     */
    public function destroy(Video $video)
    {
        $video->delete();

        return $this->itemResponse($video);
    }
}
