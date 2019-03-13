<?php

namespace App\Http\Controllers;

use App\Http\Requests\Song\CreateSongRequest;
use App\Http\Requests\Song\DestroySongRequest;
use App\Models\Song;
use App\Models\SongVideo;
use App\Models\Video;
use App\Transformers\SongTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        $songs = Song::orderBy('updated_at', 'desc')
                        ->with('song_videos', 'videos');
        
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
        
        
        if($request->input('song_videos')) {


            foreach($request->input('song_videos') as $song_video)
            {

                $sv = SongVideo::firstOrNew([
                    'song_id' => $song->id,
                    'video_id' => $song_video['video']['id']
                ]);

                $sv->volume = $song_video['volume'];
                $sv->order_id = $song_video['order_id'];

                $sv->save();

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
