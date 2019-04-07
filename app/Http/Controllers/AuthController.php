<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\OAuth\OAuth;
use App\Transformers\UserAccountTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class AuthController extends BaseController
{
    use SendsPasswordResetEmails;

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

    public function resetPassword(Request $request)
    {
        $this->validateEmail($request);

        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        return $response == Password::RESET_LINK_SENT
                    ? $this->errorResponse(['message' => 'Password reset link sent'], 200)
                    : $this->errorResponse(['message' => 'Error sending password reset link'], 400);
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
        $user = auth()->user();

        $data = $this->createItem($user, new UserAccountTransformer(), User::class);

        return response()->json($data, 200);

    }

    public function current_user()
    {
        $user = auth()->user();

        $data = $this->createItem($user, new UserAccountTransformer(), User::class);

        return response()->json($data, 200);    

    }
    
}
