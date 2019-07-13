<?php

namespace Erpmonster\Auth\Middleware;

use Closure;

class LangMiddleware {

/**
 * Run the request filter.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  \Closure  $next
 * @return mixed
 */
    public function handle($request, Closure $next)
    {
        $locale = $request->server('HTTP_ACCEPT_LANGUAGE');
        
        if($locale){
            \App::setLocale($locale);
        }

        return $next($request);
    }
}