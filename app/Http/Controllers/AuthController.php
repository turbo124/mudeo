<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Transformers\UserTransformer;
use App\OAuth\OAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseController
{

    public function passwordAuth(Request $request)
    {
     $credentials = $request->only('email', 'password');

        if (auth()->once($credentials)) 
        {
            // Authentication passed...
            $user = User::whereEmail($request->input('email'))->first();

            if ($user) 
            {
                auth()->login($user);

                return $this->processLogin($request);
            }
            else
                return $this->errorResponse(['message' => 'Error retrieving user'], 400);

        }
        else
            return $this->errorResponse(['message' => 'Invalid credentials'], 401);
    }

    public function oauthLogin(Request $request)
    {
        $user = false;
        $token = $request->input('token');
        $provider = $request->input('provider');

        $oAuth = new OAuth();
        $user = $oAuth->getProvider($provider)->getTokenResponse($token);

        if ($user) {
            auth()->login($user);
            return $this->processLogin($request);
        }
        else
            return $this->errorResponse(['message' => 'Invalid credentials'], 401);

    }

    private function processLogin(Request $request, $createToken = true)
    {
        // Create a new token only if one does not already exist
        $user = auth()->user();
        
       // if ($createToken)
       //     $this->accountRepo->createTokens($user, $request->token_name);

        $transformer = new UserTransformer($request->serializer, $request->token_name);
        $data = $this->createItem($user, $transformer, 'user');

        return $this->response($data);
    }
    
}
