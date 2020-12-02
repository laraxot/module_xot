<?php

namespace Modules\Xot\Http\Middleware;

class SelectResponseMiddleware {
    public function handle($request, \Closure $next, ...$guards) {
        $responseType = $request['responseType'];
        //dddx('qui');
        //if ($responseType) {
        //    \Presenter::select($responseType);
        //}

        return $next($request);
    }
}
