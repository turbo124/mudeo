<?php

namespace App\Http\Controllers;

use Youtube;
use App\Filters\SongFilters;
use App\Http\Controllers\Requests\Song\CreateSongRequest;
use App\Http\Controllers\Requests\Song\DestroySongRequest;
use App\Http\Controllers\Requests\Song\UpdateSongRequest;
use App\Jobs\MakeStackedSong;
use App\Jobs\UploadSongToTwitter;
use App\Models\Song;
use App\Models\SongVideo;
use App\Models\Video;
use App\Models\User;
use App\Notifications\SongApproved;
use App\Transformers\SongTransformer;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Notifications\SongSubmitted;

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
        $user = auth()->user();

        /*
        $systemWhere = [
            ['is_public', '=', 1],
        ];
        if (! $user->isAdmin()) {
            $systemWhere[] = ['is_approved', '=', 1];
        }

        $userWhere = [
            ['user_id', '=', $user->id],
        ];
        if (! $user->hasPrivateStorage()) {
            $userWhere[] = ['is_public', '=', 1];
        }

        $songs = Song::filter($filters)
                    ->with('song_videos.video', 'user', 'comments.user')
                    ->where($systemWhere)
                    ->orWhere($userWhere)
                    ->orderByRaw("CASE WHEN `songs`.`user_id` = {$user->id} THEN 0 ELSE 1 END ASC");
        */

        $count = Song::whereUserId($user->id)->count();

        $userWhere = [
            ['user_id', '=', $user->id],
        ];
        if (! $user->hasPrivateStorage()) {
            $userWhere[] = ['is_public', '=', 1];
        }

        $songs = Song::filter($filters)
            ->with('song_videos.video', 'user', 'comments.user')
            ->where([
                ['is_approved', '=', 1],
                ['is_public', '=', 1],
            ])
            ->orWhere($userWhere)
            ->orderByRaw("CASE WHEN `songs`.`user_id` = {$user->id} THEN 99999999999 ELSE `songs`.`id` END DESC")
            ->limit(100 + $count);

        return $this->listResponse($songs);
    }

    public function opneIndex(SongFilters $filters)
    {
        $songs = Song::filter($filters)
                    ->with('song_videos.video', 'user', 'comments.user')
                    ->where('is_approved', '=', 1)
                    ->where('is_public', '=', 1)
                    ->orderBy('id', 'desc')
                    ->limit(100);

        return $this->listResponse($songs);
    }

    public function userSongs(SongFilters $filters)
    {
        $user = auth()->user();

        $userWhere = [
            ['user_id', '=', $user->id],
        ];
        if (! $user->hasPrivateStorage()) {
            $userWhere[] = ['is_public', '=', 1];
        }

        $songs = Song::filter($filters)
            ->with('song_videos.video', 'user', 'comments.user')
            ->where($userWhere)
            ->orWhereHas('users', function($query) use ($user) {
                $query->where('user_id', '=', $user->id);
            })
            ->orderBy('id', 'desc');

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
        $user = auth()->user();
        $song = Song::create($request->all());
        $song->needs_render = true;

        if ($user->hasPrivateStorage() && ! filter_var($request->is_public, FILTER_VALIDATE_BOOLEAN)) {
            $song->is_public = false;
        }

        $song->save();

        $hashids = new Hashids('', 10);

        $song->url = config('mudeo.app_url') . '/song/' . $hashids->encode($song->id);
        $song->video_url = config('mudeo.asset_url') . 'videos/' . $hashids->encode( $song->user_id ) . '/' . Str::random(40) . '.mp4';

        $song->save();

        if ($request->input('song_videos')) {
            foreach($request->input('song_videos') as $song_video)
            {
                $sv = new SongVideo();
                $sv->song_id = $song->id;
                $sv->video_id = $song_video['video']['id'];
                $sv->volume = $song_video['volume'];
                $sv->order_id = $song_video['order_id'];
                $sv->delay = isset($song_video['delay']) ? $song_video['delay'] : 0;
                $sv->is_included = isset($song_video['is_included']) ? filter_var($song_video['is_included'], FILTER_VALIDATE_BOOLEAN) : true;

                $sv->save();
            }

            MakeStackedSong::dispatch($song);
        }

        return $this->itemResponse($song->fresh());
    }

    public function approve($hashedId)
    {
        if (request()->secret != config('mudeo.publish_secret')) {
            echo 'Done';
            exit;
        }

        $hashids = new Hashids('', 10);
        $hashed_id = $hashids->decode($hashedId);
        $song = Song::where('is_public', '=', true)->findOrFail($hashed_id[0]);

        if ($song->is_approved && request()->tweet != 'force') {
            return redirect($song->url);
        }

        $song->is_approved = true;
        $song->approved_at = now();
        $song->save();

        //$song->notify(new SongApproved());

        return redirect('/')->with('status', 'Song has been approved!');
    }

    public function tweet($hashedId)
    {
        if (request()->secret != config('mudeo.publish_secret')) {
            echo 'Done';
            exit;
        }

        $hashids = new Hashids('', 10);
        $hashed_id = $hashids->decode($hashedId);
        $song = Song::where('is_public', '=', true)->findOrFail($hashed_id[0]);

        UploadSongToTwitter::dispatch($song);

        return redirect('/')->with('status', 'Song has been tweeted!');
    }

    public function unapprove($hashedId)
    {
        if (request()->secret != config('mudeo.publish_secret')) {
            echo 'Done';
            exit;
        }

        $hashids = new Hashids('', 10);
        $hashed_id = $hashids->decode($hashedId);
        $song = Song::where('is_public', '=', true)->findOrFail($hashed_id[0]);

        $song->is_approved = false;
        $song->save();

        return redirect('/')->with('status', 'Song has been approved!');
    }


    public function feature($hashedId)
    {
        if (request()->secret != config('mudeo.publish_secret')) {
            echo 'Done';
            exit;
        }

        $hashids = new Hashids('', 10);
        $hashed_id = $hashids->decode($hashedId);
        $song = Song::where('is_public', '=', true)->findOrFail($hashed_id[0]);


        $song->is_featured = true;
        $song->save();

        return redirect('/')->with('status', 'Song has been featured!');
    }

    public function unfeature($hashedId)
    {
        if (request()->secret != config('mudeo.publish_secret')) {
            echo 'Done';
            exit;
        }

        $hashids = new Hashids('', 10);
        $hashed_id = $hashids->decode($hashedId);
        $song = Song::where('is_public', '=', true)->findOrFail($hashed_id[0]);


        $song->is_featured = false;
        $song->save();

        return redirect('/')->with('status', 'Song has been featured!');
    }

    public function publish($hashedId)
    {
        if (request()->secret != config('mudeo.publish_secret')) {
            echo 'Done';
            exit;
        }

        $hashids = new Hashids('', 10);
        $hashed_id = $hashids->decode($hashedId);

        $song = Song::where('is_public', '=', true)->findOrFail($hashed_id[0]);
        $song->youtube_published_id = $song->youtube_id;
        $song->save();

        Youtube::update($song->youtube_id, [
            'title' => $song->title,
            'description' => $song->url . "\n\n" . $song->description,
            'tags' => ['mudeo'],
            'category_id' => 10,
        ], 'public');

        echo 'Published...';
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

        $song = Song::where('is_public', '=', true)->findOrFail($hashed_id[0]);

        //$song = Song::find(3);

        $data = [
            'song' => $song,
        ];

        //return view($song->youtube_id ? 'song' : 'song_legacy', $data);
        return view('song_legacy', $data);
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
        $user = auth()->user();

        $song->fill($request->all());
        $song->needs_render = true;

        if ($song->sharing_mode != 'off'
            && !$song->sharing_key
            && $user->id == $song->user_id) {
            $song->sharing_key = \Str::random(20);
        }

        if ($user->hasPrivateStorage()) {
            $isPublic = filter_var($request->is_public, FILTER_VALIDATE_BOOLEAN);

            if ($isPublic && !$song->is_public) {
                User::admin()->notify(new SongSubmitted($song));
            }

            $song->is_public = $isPublic;
        }

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
            /*
            Youtube::update($song->youtube_id, [
                'title' => $song->title,
                'description' => $song->url . "\n\n" . $song->description,
                'tags' => ['mudeo'],
                'category_id' => 10,
            ], 'unlisted');
            */
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
        if ($song->youtube_id && $song->youtube_id != $song->youtube_published_id) {
            Youtube::delete($song->youtube_id);
        }

        $song->delete();

        return $this->itemResponse($song);
    }

    public function buildVideo($song_hash)
    {

        $hashids = new Hashids('', 10);
        $song_id = $hashids->decode($song_hash);

        $song = Song::findOrFail($song_id[0]);

        MakeStackedSong::dispatch($song);

        return response()->json(['building'], 200);

    }

    public function join()
    {
        $song = Song::where('sharing_key', '=', request()->sharing_key)->firstOrFail();

        $song->users()->attach(auth()->user()->id);

        return response()->json(['success'], 200);
    }

    public function leave()
    {
        $song = Song::find(request()->song_id);

        $song->users()->detach(auth()->user()->id);

        return response()->json(['success'], 200);
    }
}
