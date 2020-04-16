<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CreateUserRequest;
use App\Models\User;
use App\Transformers\UserAccountTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Hashids\Hashids;
use Storage;

class UserAccountController extends BaseController
{
    protected $entityType = User::class;
    protected $entityTransformer = UserAccountTransformer::class;

    public function create(CreateUserRequest $request) {

    	$user = User::create($request->all());
    	$user->save();
    	$user->refresh();

        if ($request->profile_image_url) {
            $contents = file_get_contents($request->profile_image_url);
            $hashids = new Hashids('', 10);
            $path = 'users/' . $hashids->encode( $user->id ) . 'jpg';
            Storage::put($path, $contents);
            $user->profile_image_url = config('mudeo.asset_url') . $path;
            $user->save();
        }

        $data = $this->createItem($user, new UserAccountTransformer(), User::class);

    	return response()->json($data, 200);

    }

    public function check_handle(Request $request)
    {
    	$user = User::whereHandle($request->input('handle'))->exists();

    	if($user)
    		return response()->json([],400);
    	else
    		return response()->json([],200);
    }


}
