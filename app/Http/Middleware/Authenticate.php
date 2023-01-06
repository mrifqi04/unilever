<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            $parameters = [];
            if (!in_array(\url()->current(), [\url()->route('login'), \url()->to('/')])) {
                $parameters['redirect'] = urlencode(\url()->current());
            }
            return route('login', $parameters);
//            return route('login');
        }
    }
}
