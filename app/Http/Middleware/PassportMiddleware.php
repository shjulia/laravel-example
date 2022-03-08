<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class PassportMiddleware
 * @package App\Http\Middleware
 */
class PassportMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     * @param null $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {

        $client = \DB::table('oauth_clients')->where('name', 'Boon Password Grant Client')->first();
        $request->request->add(['client_id' => $client->id]);
        $request->request->add(['client_secret' => $client->secret]);

        return $next($request);
    }
}
