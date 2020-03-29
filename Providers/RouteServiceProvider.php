<?php

namespace Modules\Xot\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\Str;

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
        $langs = array_keys(config('laravellocalization.supportedLocales'));

        if (in_array(\Request::segment(1), $langs)) {
            $lang = \Request::segment(1);
            \App::setLocale($lang);
        }

        $this->registerRoutePattern($router);
        /*-- e' commentato perche' devo controllare se funziona --*/
        if ('migrate' != \Request::input('act') /*&& ! $this->app->runningInConsole() */) {
            $this->registerRouteBind($router);
        }

        //$route_params = \Route::current()->parameters();
        //ddd($route_params);

        //ddd('preso');
    }

    public function registerRoutePattern(Router $router) {
        //---------- Lang Route Pattern
        $langs = config('laravellocalization.supportedLocales');
        $pattern = collect(\array_keys($langs))->implode('|');
        $pattern = '/|'.$pattern.'|/i';
        $router->pattern('lang', $pattern);
        //---------- Container ROUTE PATTERN
        $models = tenantConfig('xra.model');
        $pattern = collect(\array_keys($models))->implode('|');
        $pattern = '/|'.$pattern.'|/i';

        $pattern_test = [
            0 => $pattern, // working
            1 => '^((?!create|edit).)*$', //eppa
            2 => '[[:alnum:]_]+',
            3 => '[^[\/]+', //error edits
            4 => '^(?!.*(edit|create).*$)',
            5 => '^(?:(?!\bedit\b).)*$',
            6 => '^((?!\bedit\b).)*$',
            7 => '[[:alnum:]_?!\bedit\b]+',
        ];

        for ($i = 0; $i < 4; ++$i) {
            $container_name = 'container'.$i;
            //$router->pattern($container_name,$pattern_test[0]);
        }
    }

    //end registerRoutePattern

    public function registerRouteBind(Router $router) {
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
            $router->bind($item_name, function ($value) use ($container_name, $lang, $i) {
                $container_curr = request()->$container_name;
                $types = Str::camel(Str::plural($container_curr));

                if (0 == $i) {
                    $model = xotModel($container_curr);
                    $rows = $model;
                } else {
                    $item_prev = request()->{'item'.($i - 1)};
                    if (is_string($item_prev)) {
                        $container_prev = request()->{'container'.($i - 1)};
                        $container_prev_obj = xotModel($container_prev);
                        $item_prev = $container_prev_obj->fixItemLang($item_prev);
                    }
                    $types = Str::camel(Str::plural($container_curr));
                    $rows = $item_prev->$types();
                    $model = $rows->getRelated();
                }

                if (method_exists($model, 'scopeWithPost')) {
                    $rows = $rows->withPost($value); //scopeGlobal ?
                }
                $pk = ($model->getRouteKeyName());
                $pk_full = $model->getTable().'.'.$pk;
                if ('guid' == $pk) {
                    $pk_full = 'guid';
                } // pezza momentanea

                $value = Str::slug($value); //retrocompatibilita'
                $rows = $rows->where([$pk_full => $value]);
                $row = $rows->first();

                if (is_object($row)) {
                    return $row;
                }
                if ($debug = 0) {
                    echo PHP_EOL.'----------------------------------';
                    echo PHP_EOL.' model class : '.get_class($model);
                    echo PHP_EOL.' value : '.$value;
                    echo PHP_EOL.' pk_full : '.$pk_full;
                    echo PHP_EOL.'----------------------------------';
                    ddd(\Modules\Blog\Models\Post::count());
                }

                return $value;
            });
        }
    }

    //end registerRouteBind
}
