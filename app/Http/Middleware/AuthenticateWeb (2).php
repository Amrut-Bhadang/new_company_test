<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class AuthenticateWeb
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function handle($request, Closure $next)
    {
        $auth_user = Session::get('AuthUserData');
        if ($auth_user != null) {
            return $next($request);
        } else {
            return redirect()->route('web.home');
        }
    }
}
