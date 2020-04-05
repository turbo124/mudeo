<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CreateImageRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\DestroyUserRequest;
use App\Models\User;
use App\Models\Video;
use App\Models\Song;
use App\Transformers\UserTransformer;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Storage;
use Youtube;

class UserController extends BaseController
{

    protected $entityType = User::class;

	protected $entityTransformer = UserTransformer::class;

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->fill(request()->all());

        $user->save();

        return $this->itemResponse($user);

    }

    public function show(User $user)
    {

        return $this->itemResponse($user);

    }

    public function storeProfileImage(CreateImageRequest $request)
    {
    	$user = auth()->user();

    	$file_path = $this->storeImage($request);

		$user->profile_image_url = config('mudeo.asset_url') . $file_path;

        $user->save();

        return $this->itemResponse($user);

    }

    public function storeBackgroundImage(CreateImageRequest $request)
    {

    	$user = auth()->user();

    	$file_path = $this->storeImage($request);

		$user->header_image_url = config('mudeo.asset_url') . $file_path;

        $user->save();

        return $this->itemResponse($user);

    }

    private function storeImage($request)
    {
    	if($request->file('image')) {

            $hashids = new Hashids('', 10);

            $file_path = $request->file('image')->store( 'users/' . $hashids->encode( auth()->user()->id ) );

            if($file_path)
            	return $file_path;
            else
            	return $this->errorResponse(['message' => 'There was an issue saving this image']);

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Song  $song
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyUserRequest $request, User $user)
    {
        \Log::info('## DELETE USER ##');
        $user = auth()->user();

        if ($user->profile_image_url) {
            Storage::delete($user->profile_image_url);
        }

        if ($user->header_image_url) {
            Storage::delete($user->header_image_url);
        }

        // Delete all track videos
        $videos = Video::withTrashed()->whereUserId($user->id)->orderBy('id')->get();
        foreach ($videos as $video) {
            \Log::info('Delete video: ' . $video->url);
            \Log::info('Delete video thumbnail' . $video->thumbnail_url);
            //Storage::delete($video->url);
            //Storage::delete($video->thumbnail_url);
        }

        // Delete all songs videos
        $songs = Song::withTrashed()->whereUserId($user->id)->orderBy('id')->get();
        foreach ($songs as $song) {
            \Log::info('Delete song: ' . $song->video_url);
            \Log::info('Delete song thumbnail' . $song->thumbnail_url);
            //Storage::delete($song->video_url);
            //Storage::delete($song->thumbnail_url);

            if ($song->youtube_id && $song->youtube_id != $song->youtube_published_id) {
                \Log::info('Delete youtube video');
                //Youtube::delete($song->youtube_id);
            }
        }

        \Log::info('Delete user');
        //$user->forceDelete();

        return 'SUCCESS';
    }
}
