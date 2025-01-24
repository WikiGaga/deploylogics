<?php

namespace App\Http\Middleware;

use Closure;

class LanguageMiddleware
{
    public function handle($request, Closure $next)
    {
        $locale = Session::get('app_locale', config('app.locale'));
        App::setLocale($locale);

        return $next($request);
    }
}
