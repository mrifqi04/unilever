<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            $redirect = $request->get('redirect');
            if (!is_null($redirect)) {
                $redirect = urldecode($redirect);
            } else {
                $redirect = RouteServiceProvider::HOME;
            }
            return redirect($redirect);
//            return redirect(RouteServiceProvider::HOME);
        }

        return $next($request);
    }
}
