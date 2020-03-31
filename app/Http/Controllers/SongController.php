<?php

namespace App\Http\Controllers;

use Youtube;
use App\Filters\SongFilters;
use App\Http\Controllers\Requests\Song\CreateSongRequest;
use App\Http\Controllers\Requests\Song\DestroySongRequest;
use App\Http\Controllers\Requests\Song\UpdateSongRequest;
use App\Jobs\MakeStackedSong;
use App\Jobs\UploadSongToYouTube;
use App\Models\Song;
use App\Models\SongVideo;
use App\Models\Video;
use App\Models\User;
use App\Notifications\SongSubmitted;
use App\Notifications\SongApproved;
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
        $userId = auth()->check() ? auth()->user()->id : 0;

        $songs = Song::filter($filters)
                    ->with('song_videos.video', 'user', 'comments.user')
                    ->where('is_approved', '=', 1)
                    ->orWhere('user_id', '=', $userId);

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
                $sv->delay = isset($song_video['delay']) ? $song_video['delay'] : 0;
                $sv->is_included = isset($song_video['is_included']) ? filter_var($song_video['is_included'], FILTER_VALIDATE_BOOLEAN) : true;

                $sv->save();
            }

            MakeStackedSong::dispatch($song);
        }

        User::admin()->notify(new SongSubmitted($song));

        return $this->itemResponse($song->fresh());
    }

    public function approve($hashedId)
    {
        $hashids = new Hashids('', 10);
        $hashed_id = $hashids->decode($hashedId);
        $song = Song::findOrFail($hashed_id[0]);

        if ($song->is_approved && request()->tweet != 'force') {
            return redirect($song->url);
        }

        $song->is_approved = true;
        $song->save();

        $song->notify(new SongApproved());

        return redirect('/')->with('status', 'Song has been approved!');
    }

    public function publish($hashedId)
    {
        if (request()->secret != config('mudeo.publish_secret')) {
            echo 'Done';
            exit;
        }

        $hashids = new Hashids('', 10);
        $hashed_id = $hashids->decode($hashedId);
        $song = Song::findOrFail($hashed_id[0]);

        UploadSongToYouTube::dispatch($song, true);

        echo 'Publishing...';
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

        if (!$hashed_id) {
            abort(404);
        }

        $song = Song::findOrFail($hashed_id[0]);

        $data = [
            'song' => $song,
            'video_url' => $this->songUrl($song, $hashedId),
        ];

        return view($song->youtube_id ? 'song' : 'song_legacy', $data);
    }

    private function songUrl($song, $hashedId)
    {
        $hashids = new Hashids('', 10);
        $user_hash = $hashids->encode($song->user->id);

        return  config('mudeo.asset_url') . 'videos/' . $user_hash . '/' . $hashedId . '.mp4?updated_at=' . str_replace(' ', '_', $song->updated_at);
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
        $song->is_rendered = false;
        $song->save();

        if ($request->input('song_videos')) {

            $trackIds = [];

            foreach ($request->input('song_videos') as $song_video) {
                $id = $song_video['id'];
                if ($id > 0) {
                    $trackIds[] = $id;
                }
            }

            foreach ($song->song_videos as $song_video) {
                if (!in_array($song_video->id, $trackIds)) {
                    $song_video->delete();
                }
            }

            foreach ($request->input('song_videos') as $song_video) {
                $sv = SongVideo::whereId($song_video['id'])->first();

                if (!$sv) {
                    $sv = new SongVideo();
                    $sv->song_id = $song->id;
                    $sv->video_id = $song_video['video']['id'];
                }

                $sv->volume = $song_video['volume'];
                $sv->order_id = $song_video['order_id'];
                $sv->delay = isset($song_video['delay']) ? $song_video['delay'] : 0;
                $sv->is_included = isset($song_video['is_included']) ? filter_var($song_video['is_included'], FILTER_VALIDATE_BOOLEAN) : true;

                $sv->save();
            }

            MakeStackedSong::dispatch($song);
        } else if ($song->youtube_id) {
            Youtube::update($song->youtube_id, [
                'title' => $song->title,
                'description' => $song->url . "\n\n" . $song->description,
                'tags' => ['mudeo'],
                'category_id' => 10,
            ], 'unlisted');
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
