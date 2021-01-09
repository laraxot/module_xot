<<<<<<< HEAD
<?php

namespace Modules\Xot\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;
use Modules\Xot\Http\Middleware\SetDefaultLocaleForUrlsMiddleware;

//--- services ---

//--- bases -----

class RouteServiceProvider extends XotBaseRouteServiceProvider {
    /**
     * The module namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected string $moduleNamespace = 'Modules\Xot\Http\Controllers';
    /**
     * The module directory.
     *
     * @var string
     */
    protected string $module_dir = __DIR__;
    /**
     * The module namespace.
     *
     * @var string
     */
    protected string $module_ns = __NAMESPACE__;

    public function bootCallback() {
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
=======
<?php

namespace Modules\Xot\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;
use Modules\Xot\Http\Middleware\SetDefaultLocaleForUrlsMiddleware;

//--- services ---

//--- bases -----

/**
 * Class RouteServiceProvider
 * @package Modules\Xot\Providers
 */
class RouteServiceProvider extends XotBaseRouteServiceProvider {
    /**
     * The module namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected $moduleNamespace = 'Modules\Xot\Http\Controllers';
    /**
     * @var string
     */
    protected $module_dir = __DIR__;
    /**
     * @var string
     */
    protected $module_ns = __NAMESPACE__;

    public function bootCallback() {
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

    /**
     * @param Router $router
     */
    public function registerRoutePattern(Router $router) {
        //---------- Lang Route Pattern
        $langs = config('laravellocalization.supportedLocales');
        $pattern = collect(\array_keys($langs))->implode('|');
        $pattern = '/|'.$pattern.'|/i';
        $router->pattern('lang', $pattern);
    }

    //end registerRoutePattern
}
>>>>>>> c906275 (.)
