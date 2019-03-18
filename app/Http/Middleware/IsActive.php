<?php

namespace App\Http\Middleware;

use Closure;

class IsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (\Auth::user() && \Auth::user()->active == 1) {
            return $next($request);
        }

        // You've been disabled. Logging out.
        \Auth::logout();
        return redirect()->guest('login')->withErrors(["Your account is not active."]);
    }
}
