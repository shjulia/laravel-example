<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;

/**
 * Class CheckReferralCode
 * @package App\Http\Middleware
 */
class CheckReferralCode
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->code) {
            return $next($request);
        }

        if (strlen($request->code) < 20) {
            return $next($request);
        }

        $newCode = substr($request->code, 0, 5);
        $request->merge(['code' => $newCode]);

        return $next($request);
    }
}
