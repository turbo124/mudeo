<?php

namespace App\Http\Controllers;

use App\Ninja\Transformers\UserTransformer;
use App\OAuth\OAuth;
use Illuminate\Http\Request;

class AuthController extends BaseController
{

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
        
        if ($createToken)
            $this->accountRepo->createTokens($user, $request->token_name);

        $transformer = new UserTransformer($request->serializer, $request->token_name);
        $data = $this->createCollection($users, $transformer, 'user');

        return $this->response($data);
    }
    
}
