<?php

namespace App\Http\Middleware;

use Closure, Session;
// use App;

class Localization
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

        if (isset($request->language)) {
            \App::setLocale($request->language);
        } else {
            \App::setLocale(Session::get('locale'));
        }

        return $next($request);
    }
}
