<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class Cors
{
    public function handle($request, Closure $next)
    {
        if ($request->getMethod() == "OPTIONS") {
            header("Access-Control-Allow-Origin: *");

            // ALLOW OPTIONS METHOD
            $headers = [
                'Access-Control-Allow-Methods'=> 'POST, GET, OPTIONS, PUT, DELETE',
                'Access-Control-Allow-Headers'=> 'X-API-SECRET,X-API-TOKEN,X-API-PASSWORD,DNT,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type,Range'
            ];

            return Response::make('OK', 200, $headers);
        }

        $response = $next($request);

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'X-API-SECRET,X-API-TOKEN,X-API-PASSWORD,DNT,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type,Range');
        $response->headers->set('X-APP-VERSION', config('ninja.app_version'));
        $response->headers->set('X-API-VERSION', config('ninja.api_version'));

        return $response;
    }
}
