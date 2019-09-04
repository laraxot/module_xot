<?php

namespace Modules\Xot\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider; // per dizionario morph
use Illuminate\Support\Facades\Route;

abstract class XotBaseRouteServiceProvider extends ServiceProvider {
    /**
     * The module namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    //protected $moduleNamespace = 'Modules\LU\Http\Controllers';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     */
    public function boot() {
        //*
        //ddd(\Request::segment(2));// trasferte_dip
        /*
        if($this->module_name!='lu'){
            ddd($this->module_name);
        }
        */
        \Config::set('extra_conn', \Request::segment(2)); //MOMENTANEO PER TIRARE SU
        /*
        if(in_admin() && \Request::segment(2)==$this->module_name) {
            $mod_models=getModuleModels($this->module_name);
            //ddd($mod_models);
            //ddd($mod_models);// correggere LU che viene scritto l_u ?
            // tenant => inquilino
            $xra_models=tenantConfig('xra.model');
            $merge_models=array_merge($xra_models,$mod_models);
            \Config::set('xra.model', $merge_models);
            Relation::morphMap($merge_models);
            $router=$this->app['router'];
            $this->registerRoutePattern($router);
        }
        //*/
        if (method_exists($this, 'bootCallback')) {
            $this->bootCallback();
        }
        parent::boot();
    }

    /*
    public function registerRoutePattern(\Illuminate\Routing\Router $router){
        //----------ROUTE PATTERN
        if (\is_array(config('xra.model'))) {
            $pattern = \implode('|', \array_keys(config('xra.model')));
            $patternC = \str_replace('|', ' ', $pattern);
            $patternC = \ucwords($patternC);
            $patternC = \str_replace(' ', '|', $patternC);
            $pattern .= '|'.$patternC;
            for ($i = 0; $i < 4; ++$i) {
                $container_name = 'container';
                $container_name .= $i;
                $router->pattern($container_name, '/|'.$pattern.'|/i');
            }
        }else{

        }
    }
    */

    /**
     * Define the routes for the application.
     */
    public function map() {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapWebRoutes() {
        Route::middleware('web')
            ->namespace($this->moduleNamespace)
            ->group($this->module_dir.'/../Routes/web.php');
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApiRoutes() {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->moduleNamespace)
            ->group($this->module_dir.'/../Routes/api.php');
    }
}
