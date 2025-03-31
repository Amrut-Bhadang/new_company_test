<?php

namespace App\Http\Middleware;

use Closure;
use App;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class LanguageChange
{

    public function handle(Request $request, Closure $next)
    {
        $lang = $request->server('HTTP_ACCEPT_LANGUAGE');
        App::setLocale($lang);
        $session = new Session;
        $session->set('locale', $lang);
        return $next($request);
    }
}
