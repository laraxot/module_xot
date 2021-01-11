<?php

declare(strict_types=1);

namespace Modules\Xot\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;
use Modules\Xot\Http\Middleware\SetDefaultLocaleForUrlsMiddleware;

//--- services ---

//--- bases -----

class RouteServiceProvider extends XotBaseRouteServiceProvider {
    /**
     * The module namespace to assume when generating URLs to actions.
     */
    protected string $moduleNamespace = 'Modules\Xot\Http\Controllers';

    /**
     * The module directory.
     */
    protected string $module_dir = __DIR__;

    /**
     * The module namespace.
     */
    protected string $module_ns = __NAMESPACE__;

    public function bootCallback():void {
        $router = $this->app['router'];
        //--- cambio lingua --
        $langs = array_keys(config('laravellocalization.supportedLocales'));

        if (in_array(\Request::segment(1), $langs)) {
            $lang = \Request::segment(1);
            App::setLocale($lang);
        }

        $this->registerRoutePattern($router);

        //-----------------

        //$router->pushMiddlewareToGroup('web', SetDefaultLocaleForUrlsMiddleware::class);
        $router->prependMiddlewareToGroup('web', SetDefaultLocaleForUrlsMiddleware::class);
    }

    public function registerRoutePattern(Router $router) {
        //---------- Lang Route Pattern
        $langs = config('laravellocalization.supportedLocales');
        $pattern = collect(\array_keys($langs))->implode('|');
        $pattern = '/|'.$pattern.'|/i';
        $router->pattern('lang', $pattern);
    }

    //end registerRoutePattern
}
