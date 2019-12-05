<?php

namespace Modules\Xot\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider; // per dizionario morph
use Illuminate\Support\Facades\Route;

abstract class XotBaseRouteServiceProvider extends ServiceProvider {
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
