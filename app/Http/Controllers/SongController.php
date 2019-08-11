<?php

namespace App\Http\Controllers;

use App\Filters\SongFilters;
use App\Http\Controllers\Requests\Song\CreateSongRequest;
use App\Http\Controllers\Requests\Song\DestroySongRequest;
use App\Http\Controllers\Requests\Song\UpdateSongRequest;
use App\Jobs\MakeStackedSong;
use App\Models\Song;
use App\Models\SongVideo;
use App\Models\Video;
use App\Transformers\SongTransformer;
use Hashids\Hashids;
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

    public function __construct(SongFilters $filter)
    {
        parent::__construct();

        $this->filter = $filter;
    }

    public function index(SongFilters $filters)
    {
        $songs = Song::filter($filters)
            ->with('song_videos.video', 'user', 'comments.user');

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

        $hashids = new Hashids('', 10);

        $song->url = config('mudeo.app_url') . '/song/' . $hashids->encode($song->id);
        $song->video_url = config('mudeo.asset_url') . 'videos/' . $hashids->encode( $song->user_id ) . '/' . $hashids->encode( $song->id ) . '.mp4';

        $song->save();

        if ($request->input('song_videos')) {

            foreach($request->input('song_videos') as $song_video)
            {
                $sv = SongVideo::firstOrNew([
                    'song_id' => $song->id,
                    'video_id' => $song_video['video']['id']
                ]);

                $sv->volume = $song_video['volume'];
                $sv->order_id = $song_video['order_id'];

                if (isset($song_video['delay'])) {
                    $sv->delay = $song_video['delay'];
                }

                $sv->save();
            }

            MakeStackedSong::dispatch($song);
        }

        return $this->itemResponse($song->fresh());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Song  $song
     * @return \Illuminate\Http\Response
     */
    public function show(Song $song)
    {
        $song->load('comments.user');

        return $this->itemResponse($song);

    }

    public function play($hashedId)
    {

        $hashids = new Hashids('', 10);

        $hashed_id = $hashids->decode($hashedId);


        if($hashed_id)
        {

            $song = Song::findOrFail($hashed_id[0]);

            $data = [
                'song' => $song,
                'video_url' => $this->songUrl($song, $hashedId),
            ];

            return view('song', $data);
        }

    }

    private function songUrl($song, $hashedId)
    {
        $hashids = new Hashids('', 10);
        $user_hash = $hashids->encode($song->user->id);

        return  config('mudeo.asset_url') . 'videos/' . $user_hash . '/' . $hashedId . '.mp4?updated_at=' . $song->updated_at;

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
    public function update(UpdateSongRequest $request, Song $song)
    {
        $song->fill($request->all());

        $song->save();

        if ($request->input('song_videos')) {

            $trackIds = [];

            foreach ($request->input('song_videos') as $song_video) {
                \Log::error('Lookup track id ' . $song_video['id']);
                $sv = SongVideo::whereId($song_video['id'])->first();

                if (!$sv) {
                    $sv = new SongVideo();
                    $sv->song_id = $song->id;
                    $sv->video_id = $song_video['video']['id'];

                    \Log::error('Not found, creating new track..');
                }

                $sv->volume = $song_video['volume'];
                $sv->order_id = $song_video['order_id'];
                $sv->delay = isset($song_video['delay']) ? $song_video['delay'] : 0;
                $sv->is_included = isset($song_video['is_included']) ? filter_var($song_video['is_included'], FILTER_VALIDATE_BOOLEAN) : true;

                $sv->save();
                $sv->fresh();

                $trackIds[] = $sv->id;

                \Log::error('Adding track id: ' . $sv->id);
            }

            \Log::error('Clean up ');
            foreach ($song->song_videos as $song_video) {
                \Log::error('id: ' . $song_video->id);
                \Log::error('in array: ' . in_array($song_video->id, $trackIds));

                if (!in_array($song_video->id, $trackIds)) {
                    \Log::error('deleting');
                    $song_video->delete();
                } else {
                    \Log::error('not deleting');
                }
            }

            MakeStackedSong::dispatch($song);
        }

        return $this->itemResponse($song->fresh());

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

    public function buildVideo($song_hash)
    {

        $hashids = new Hashids('', 10);
        $song_id = $hashids->decode($song_hash);

        $song = Song::findOrFail($song_id[0]);

        MakeStackedSong::dispatch($song);

        return response()->json(['building'],200);

    }

}
