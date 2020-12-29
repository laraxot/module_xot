<?php

namespace Modules\Xot\Http\Middleware;

/*
 * https://laravel.com/docs/8.x/urls#default-values
 */

use Closure;
use Illuminate\Support\Facades\URL;

class SetDefaultLocaleForUrlsMiddleware {
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function handle($request, Closure $next) {
        URL::defaults(
            [
                'lang' => app()->getLocale(),
                //'referrer' => url()->previous(),
            ]
        );

        return $next($request);
    }
}
