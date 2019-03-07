<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CreateUserRequest;
use App\Models\User;
use App\Transformers\UserAccountTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserAccountController extends BaseController
{
    
    public function create(CreateUserRequest $request) {

    	$user = User::create($request->all());

    	$user->save();
    	$user->refresh();


        $transformer = new UserAccountTransformer();

        $data = $this->createItem($user, $transformer, User::class);

    	return response()->json($data, 200);

    }

}
