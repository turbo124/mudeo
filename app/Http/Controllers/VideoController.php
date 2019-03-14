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
                'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
                'ffprobe.binaries' => '/usr/bin/ffprobe' 
            ]);


            //$ffmpeg = FFMpeg::create();

            $tmp_file_name = sha1(time()) . '.jpg';

            $vid = $ffmpeg->open($request->file('video'));
            $vid_object = $vid->frame(TimeCode::fromSeconds(1))->save('', false, true);

            //$tmp_file = Storage::disk('local')->put($tmp_file_name , base64_decode($vid_object));
            $tmp_file = Storage::disk('local')->put($tmp_file_name , $vid_object);

            Log::error($tmp_file);

            $disk = Storage::disk('gcs');

            $remote_storage_file_name = 'videos/' . $hashids->encode( auth()->user()->id ) . '/' . $hashids->encode( auth()->user()->id ) . '_' .$tmp_file_name;

            $disk->put($remote_storage_file_name, Storage::disk('local')->get($tmp_file_name));

            Storage::disk('local')->delete($tmp_file_name);

            $video->thumbnail_url = $disk->url($remote_storage_file_name);
            $video->save();
  
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
