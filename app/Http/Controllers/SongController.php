<?php

namespace App\Http\Controllers;

use App\Http\Requests\Song\CreateSongRequest;
use App\Http\Requests\Song\DestroySongRequest;
use App\Models\Song;
use App\Models\Video;
use App\Transformers\SongTransformer;
use Illuminate\Http\Request;

class SongController extends BaseController
{

    protected $entityType = Song::class;
    protected $entityTransformer = SongTransformer::class;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $songs = Song::orderBy('updated_at', 'desc');
        
        return $this->listResponse($songs);

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
    public function store(CreateSongRequest $request)
    {
        $song = Song::create($request->all());
        $song->save();
        
        if($request->input('videos')) {

            foreach($request->input('videos') as $request_video)
            {

            $video = Video::create($request_video)->save();
            $song->videos()->sync($video);

            $song->videos()->updateExistingPivot($video->id, ['volume' => $request_video['volume'], 'order_id' => $request_video['order_id']]);

            }
            
        }


        return $this->itemResponse($song);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Song  $song
     * @return \Illuminate\Http\Response
     */
    public function show(Song $song)
    {
        return $this->itemResponse($song);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Song  $song
     * @return \Illuminate\Http\Response
     */
    public function edit(Song $song)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Song  $song
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Song $song)
    {
        $song->fill($request->all());
        $save->save();

        return $this->itemResponse($song);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Song  $song
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroySongRequest $request, Song $song)
    {
        $song->delete();

        return $this->itemResponse($song);
    }
}
