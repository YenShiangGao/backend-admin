<?php

namespace App\Http\Api\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Api\Resources\LoginResource;

/**
 * Class RedirectIfAuthenticated
 * @package App\Http\Api\Middleware
 */
class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (auth()->check()) {
            return new LoginResource(['token' => auth()->getToken()->get()]);
        }

        return $next($request);
    }
}
