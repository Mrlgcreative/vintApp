<?php

namespace Tests\Unit;

use App\Http\Middleware\CacheResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CacheResponseTest extends TestCase
{
    /**
     * Un cache injoignable (base en lecture seule, disque plein) ne doit pas
     * transformer un endpoint public en 500 : on sert la réponse sans cacher.
     */
    public function test_serves_the_response_when_the_cache_is_unavailable()
    {
        $down = new \RuntimeException('UPDATE command denied');
        Cache::shouldReceive('get')->andThrow($down);
        Cache::shouldReceive('put')->andThrow($down);

        $request = Request::create('/api/items', 'GET');
        $request->headers->set('Accept', 'application/json');

        $response = (new CacheResponse)->handle($request, function () {
            return response()->json(['data' => []]);
        });

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('MISS', $response->headers->get('X-Cache'));
    }
}
