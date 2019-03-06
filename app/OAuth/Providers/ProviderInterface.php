<?php 

namespace App\OAuth\Providers;

interface ProviderInterface
{
    public function getTokenResponse($token);

    public function harvestEmail($response);

}
