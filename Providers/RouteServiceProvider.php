<?php

namespace Modules\Xot\Providers;

use Illuminate\Support\Facades\Route;

//--- bases ---

class RouteServiceProvider extends XotBaseRouteServiceProvider {
    /**
     * The module namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected $moduleNamespace = 'Modules\Xot\Http\Controllers';
    protected $module_dir = __DIR__;
    protected $module_ns = __NAMESPACE__;

    public function bootCallback() {
        $router = $this->app['router'];
        //--- cambio lingua --
        $langs=array_keys(config('laravellocalization.supportedLocales'));
        
        if(in_array(\Request::segment(1),$langs)){
            $lang=\Request::segment(1);
            \App::setLocale($lang);
        };

        //$this->mergeConfigs();
        $this->registerRoutePattern($router);
        if ('migrate' != \Request::input('act') && ! $this->app->runningInConsole()) {
            $this->registerRouteBind($router);
        }
        //ddd('preso');
    }

    public function registerRoutePattern(\Illuminate\Routing\Router $router) {
        $models = tenantConfig('xra.model');
        //----------ROUTE PATTERN
        $pattern = collect(\array_keys($models))->implode('|');
        $pattern = '/|'.$pattern.'|/i';
        for ($i = 0; $i < 4; ++$i) {
            $container_name = 'container'.$i;
            $router->pattern($container_name, $pattern);
        }
    }

    //end registerRoutePattern

    public function registerRouteBind(\Illuminate\Routing\Router $router) {
        //--------- ROUTE BIND
        //*
        $router->bind('lang', function ($value) {
            \App::setLocale($value);

            return $value;
        });
        $lang = \App::getLocale();
        for ($i = 0; $i < 4; ++$i) {
            $item_name = 'item'.$i;
            $container_name = 'container'.$i;
            $router->bind($item_name, function ($value) use ($container_name,$lang,$i) {
                $container_curr = request()->$container_name;
                $types = camel_case(str_plural($container_curr));

                if (0 == $i) {
                    $model = xotModel($container_curr);
                    $rows = $model;
                } else {
                    $item_prev = request()->{'item'.($i - 1)};
                    $types = camel_case(str_plural($container_curr));
                    $rows = $item_prev->$types();
                    $model = $rows->getRelated();
                }

                if (method_exists($model, 'scopeWithPost')) {
                    $rows = $rows->withPost($value);  //scopeGlobal ?
                }
                $pk = ($model->getRouteKeyName());
                $pk_full = $model->getTable().'.'.$pk;
                if ('guid' == $pk) {
                    $pk_full = 'guid';
                } // pezza momentanea
                $rows = $rows->where([$pk_full => $value]);
                $row = $rows->first();

                if (is_object($row)) {
                    return $row;
                }

                return $value;
            });
        }
    }

    //end registerRouteBind
}
