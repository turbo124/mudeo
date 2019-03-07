<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CreateUserRequest;
use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserAccountController extends BaseController
{
    
    public function create(CreateUserRequest $request) {

    	$user = User::create([
    		'email' => $request->input('email'),
    		'password' => Hash::make($request->input('password'))
    	]);

    	$user->save();
    	$user->refresh();


        $transformer = new UserTransformer();

        $data = $this->createItem($user, $transformer, User::class);

    	return response()->json($data, 200);

    }

}
