<?php

namespace Modules\Xot\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

//--- bases ---
use Modules\Xot\Providers\XotBaseRouteServiceProvider;


class RouteServiceProvider extends XotBaseRouteServiceProvider{
    /**
     * The module namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected $moduleNamespace = 'Modules\Xot\Http\Controllers';
    protected $module_dir= __DIR__;
    protected $module_ns=__NAMESPACE__;

    public function bootCallback(){

        $router=$this->app['router'];
        //$this->mergeConfigs();
        $this->registerRoutePattern($router);
        if(\Request::input('act')!='migrate'  && !$this->app->runningInConsole() ){
            $this->registerRouteBind($router);
        }
        //ddd('preso');
    }

    public function registerRoutePattern(\Illuminate\Routing\Router $router){
        $models=tenantConfig('xra.model');
        //----------ROUTE PATTERN
            $pattern=collect(\array_keys($models))->implode('|');
            $pattern= '/|'.$pattern.'|/i';
            
            //$pattern= '/'.$pattern.'/i';
            //*/
            //ddd($pattern);
            for ($i = 0; $i < 4; $i++) {
                $container_name = 'container'.$i;
                //$re = '/^(?!edit).*$/'; //--- da controllare
                //$re='/^((?!badword).)*$/';
                //$pattern='/^|edit|/i';
                //^(?:(?!\berror\b).)*$
                //$router->pattern($container_name, $pattern);
                $router->pattern($container_name, $pattern);
                //Route::pattern($container_name, '[0-9]+'); 
                //echo '<br>'.$container_name.'  '.$pattern;
            }
            //$router->pattern('module','/blog/i');
        
    }//end registerRoutePattern

     public function registerRouteBind(\Illuminate\Routing\Router $router){
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
                //container e' il nome "singolare" della relazione, percio' il modello puo' essere diverso
                /*
                if($i>0){
                    $item_prev = request()->{'item'.($i - 1)};
                }else{
                    $model=xotModel($container_curr); 
                }
                $pk=($model->getRouteKeyName());
                $pk_full=$model->getTable().'.'.$pk;

                if($pk=='guid') $pk_full='guid'; // pezza momentanea
                */
                if ($i == 0) {
                    $model=xotModel($container_curr); 
                    if(method_exists($model, 'scopeWithPost')){
                        $rows=$model->withPost($value);  //scopeGlobal ?
                    }else{
                        $rows=$model;
                    }
                    $pk=($model->getRouteKeyName());
                    $pk_full=$model->getTable().'.'.$pk;                    
                    if($pk=='guid') $pk_full='guid'; // pezza momentanea

                    $rows=$rows->where([$pk_full=>$value]);
                    //ddd($rows->toSql());
                    //ddd($rows->toSql());
                    $row=$rows->first();                   
                    //$row=$rows->find($value);
                } else {
                    $item_prev = request()->{'item'.($i - 1)};
                    $types = camel_case(str_plural($container_curr));
                    $rows=$item_prev->$types();
                    $related=$rows->getRelated();
                    $pk=($related->getRouteKeyName());
                    $pk_full=$related->getTable().'.'.$pk;                    

                    $row= $rows->where([$pk_full=>$value])->first();
                }
                if (is_object($row)) {
                    return $row;
                }
                return $value;
            });
        }
    }//end registerRouteBind


}
