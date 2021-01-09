<?php

namespace Modules\Xot\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider; // per dizionario morph
use Illuminate\Support\Facades\Route;

/**
 * Class XotBaseRouteServiceProvider
 * @package Modules\Xot\Providers
 */
abstract class XotBaseRouteServiceProvider extends ServiceProvider {
    /**
<<<<<<< HEAD
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
=======
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
>>>>>>> c906275 (.)

    public function boot() {
        \Config::set('extra_conn', \Request::segment(2)); //Se configurato va a prendere db diverso
        if (method_exists($this, 'bootCallback')) {
            $this->bootCallback();
        }
        parent::boot();
    }

    public function map() {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    protected function mapWebRoutes() {
        Route::middleware('web')
            ->namespace($this->moduleNamespace)
            ->group($this->module_dir.'/../Routes/web.php');
    }

    protected function mapApiRoutes() {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->moduleNamespace)
            ->group($this->module_dir.'/../Routes/api.php');
    }
}
