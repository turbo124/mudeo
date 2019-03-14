<?php

namespace App\Http\Controllers;

use App\Http\Requests\Video\CreateVideoRequest;
use App\Models\Song;
use App\Models\Video;
use App\Transformers\VideoTransformer;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
        Log::error($request->all());
        
        $video = Video::create($request->all());
        
        $video->save();

        if($request->input('song_id')) {

            $song = Song::find($request->input('song_id'))->first();

            $song->videos()->sync($video);

        }

        if($request->file('video')) {

            $hashids = new Hashids('', 10);

            $file_path = $request->file('video')->store( 'videos/' . $hashids->encode( auth()->user()->id ) );

            $video->url = config('mudeo.asset_url') . $file_path;
            $video->save();


            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries'  => '/usr/local/bin/ffmpeg',
                'ffprobe.binaries' => '/usr/local/bin/ffprobe' 
            ]);


            // $ffmpeg = FFMpeg::create();
/*
            $tmp_file_name = sha1(time()) . '.jpg';

            $vid = $ffmpeg->open($request->file('video'));

            $tmp_file = Storage::disk('local')->put(public_path($tmp_file_name , $vid->frame(TimeCode::fromSeconds(1))->save($hashids->encode('', false, true)));

            $file_path = Storage::disk('gcs')->put('videos/' . $hashids->encode( auth()->user()->id ) . '/' , $tmp_file);

            Storage::disk('local')->delete($tmp_file_name);

            $video->thumbnail_url = config('mudeo.asset_url') . $file_path;
  */
        }      

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
