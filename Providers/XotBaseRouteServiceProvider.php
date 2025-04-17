<?php

declare(strict_types=1);

namespace Modules\Xot\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;

/**
 * Class XotBaseRouteServiceProvider.
 */
abstract class XotBaseRouteServiceProvider extends RouteServiceProvider
{
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

    /**
     * Undocumented function.
     *
     * @return void
     */
    public function boot()
    {
        \Config::set('extra_conn', \Request::segment(2)); // Se configurato va a prendere db diverso
        if (method_exists($this, 'bootCallback')) {
            $this->bootCallback();
        }
        parent::boot();
    }

    /**
     * Undocumented function.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    /**
     * Undocumented function.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->moduleNamespace)
            ->group($this->module_dir.'/../Routes/web.php');
    }

    /**
     * Undocumented function.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->moduleNamespace)
            ->group($this->module_dir.'/../Routes/api.php');
    }
}
