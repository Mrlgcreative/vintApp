<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies; // Configuration dynamique basée sur l'environnement
    
    public function __construct()
    {
        // En développement : faire confiance à localhost et Docker
        if (app()->environment('local', 'development')) {
            $this->proxies = ['127.0.0.1', '::1', '172.18.0.0/16', '172.17.0.0/16'];
        } 
        // En production : utiliser la variable d'environnement TRUSTED_PROXIES
        else {
            $trustedProxies = env('TRUSTED_PROXIES', '');
            $this->proxies = $trustedProxies ? explode(',', $trustedProxies) : [];
        }
    }

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}