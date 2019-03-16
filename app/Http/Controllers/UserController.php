<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CreateImageRequest;
use App\Models\User;
use App\Transformers\UserTransformer;
use Hashids\Hashids;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    
    protected $entityType = User::class;
	protected $entityTransformer = UserTransformer::class;

    public function update(User $user)
    {
        $user->fill(request()->all());
        $user->save();

        return $this->itemResponse($user);
    }

    public function show()
    {
        return $this->itemResponse(auth()->user());
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
}
