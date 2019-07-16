<?php

namespace Modules\Xot\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use Illuminate\Support\Str;

use Modules\Extend\Services\StubService;

//use Modules\Extend\Traits\CrudContainerItemNoPostTrait as CrudTrait;

abstract class BaseContainerController extends Controller{

    protected $controller;
    protected $row;
    protected $module;
    protected $controller_exist;

    //public function __construct() { //o lo chiamavo "init".. etc etc
    public function init($params) { //o lo chiamavo "init".. etc etc
        //$params = \Route::current()->parameters();
        //ddd($params);
        list($containers,$items)=params2ContainerItem($params);
        $tmp=collect($containers)->map(function ($item){
        	return Str::studly($item);
        })->implode('\\');
        
        $container_first=array_first($containers);
        //ddd($container_first); //restaurant
        $model_name=config('xra.model.'.$container_first);// ddd($model_name); //Modules\Food\Models\Restaurant
        $pos=strpos($model_name,'\\Models\\');
        $mod=substr($model_name,0,$pos);
        //$controller='\Modules\\'.$mod->name.'\Http\Controllers\\'.$tmp.'Controller';
        $controller=$mod.'\Http\Controllers\\'.$tmp.'Controller';
        try{
            if(class_exists($controller)){
                $this->controller=$controller;
            }else{
                $controller='\Modules\Xot\Http\Controllers\XotBaseController';
                $this->controller=$controller;
            }
        }catch(\Exception $e){
            $controller='\Modules\Xot\Http\Controllers\XotBaseController';
            $this->controller=$controller;
        }
        $this->item_last=last($items);
        $this->container_last=last($containers);
        $this->last=last($params);
        return 'init';
    }

	public function __call_test($method, $args){
        $params = \Route::current()->parameters();
        $request=Request::capture();
        $a=$this->init($params);
        $controller = $this->controller;
        $row=$this->last;
        if(!is_object($row) && $row!=''){
            $class=config('xra.model.'.$row);
            $row=new $class;
        }
        //* -- da rimettere !
        if(!\Auth::check() && is_object($row) && in_array($method,['update','create','edit','store']) ){
            $method_name=$method.'SpecialCase';
            if(method_exists($row, $method_name)){
                return $row->$method_name();
            }
            ddd('non autorizzato ['.$method.']['.get_class($row).'][Not Logged]');
            abort(403);
        } 
        if (\Auth::check() && is_object($row) && \Auth::user()->cannot($method, $row) && !in_array($method,['index','show']) ) {
            ddd('non autorizzato ['.$method.']['.get_class($row).']');
            abort(403);
        }
        //ddd(app($controller)->authorize('post'));
        //ddd(get_class_methods(policy($row)));
        //*/
        //ddd($row);
        return app($controller)->$method($request,$this->container_last,$this->item_last);

    }

    public function __call($method, $args){
        //ddd($method);//indexEdit
        $params = \Route::current()->parameters();
        $request=Request::capture();
        $a=$this->init($params);
        $controller = $this->controller;
        $row=$this->last;
        /*
        if($row=='edit'){
            $row=collect($params)->take(-2)->first();
        }
        */
        if(!is_object($row) && $row!=''){
            $class=config('xra.model.'.$row);
            try{
                $row=new $class;
            }catch(\Exception $e){
                ddd('['.$row.']['.$class.'] not exists on config/xra.php');
            }
        } 
        if(is_object($row)){
            $panel=StubService::getByModel($row,'panel',$create = true); 
            $policy=StubService::getByModel($row,'policy',$create = true); 
            //ddd(get_class($policy));
        }
        /*
        if (!\Auth::check() || \Auth::user()->cannot('indexEdit', $row)) {
            //ddd('non autorizzato ['.$method.'][Not Logged]');  
            $routename = \Route::current()->getName();
            ddd($routename);
            $route_next=str_replace('.index_edit','.index',$routename);
            return redirect()->route($route_next,$params);
        }
        */
        //*
        if(!\Auth::check() && !in_array($method,['index','show']) ){
            $method_name=$method.'SpecialCase';
            if(method_exists($panel, $method_name)){
                return $panel->$method_name();
            }
            ddd('non autorizzato ['.$method.']['.get_class($row).'][Not Logged]');
            abort(403);
        }
        if (\Auth::check() && is_object($row) && \Auth::user()->cannot($method, $row) ){
            ddd('non autorizzato ['.$method.']['.get_class($row).'][Logged]');
            abort(403);
        }
        //*/
        

        //ddd($method);//indexEdit
        //ddd($controller);
        return app($controller)->$method($request,$this->container_last,$this->item_last);        
    }


    /*------------
    $request = \str_replace('\\Controllers\\', '\\Requests\\', $controller);
            $request = \mb_substr($request, 0, -\mb_strlen('Controller'));
            $pos = \mb_strrpos($request, '\\');
            $request = \mb_substr($request, 0, $pos + 1).studly_case($method).\mb_substr($request, $pos + 1);
            $request = $request::capture();
            $request->validate($request->rules(), $request->messages());
    -------------*/
}
