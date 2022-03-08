<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Class IsApi
 * @package App\Http\Middleware
 */
class IsApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $parts = explode('/', $request->path());
        $request->merge(['isApi' => array_shift($parts) == 'api']);
        return $next($request);
    }
}
