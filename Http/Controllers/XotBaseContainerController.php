<?php

namespace Modules\Xot\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use Illuminate\Support\Str;

use Modules\Extend\Services\StubService;

use Illuminate\Support\Facades\Gate; //4 guestPolicy

//use Modules\Extend\Traits\CrudContainerItemNoPostTrait as CrudTrait;

abstract class XotBaseContainerController extends Controller{

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
                $controller='\Modules\Xot\Http\Controllers\XotController';
                $this->controller=$controller;
            }
        }catch(\Exception $e){
            $controller='\Modules\Xot\Http\Controllers\XotController';
            $this->controller=$controller;
        }
        $this->item_last=last($items);
        $this->container_last=last($containers);
        $this->last=last($params);
        return 'init';
    }


    public function __call_test($method, $args){
        $params = \Route::current()->parameters();
        list($containers,$items)=params2ContainerItem($params);
        $request=Request::capture();
        $a=$this->init($params);
        $controller = $this->controller;
        $last_container=last($containers);
        $class=config('xra.model.'.$last_container);
        try{ $row=new $class;
        }catch(\Exception $e){ ddd('['.$row.']['.$class.'] not exists on config/xra.php'); }
        $panel=StubService::getByModel($row,'panel',$create = true); 
        $policy=StubService::getByModel($row,'policy',$create = true);
        if(\Auth::check()){
            $authorized=\Auth::user()->can($method, $row); 
        }else{
            $authorized=Gate::allows($method, $row);
        }
        if(!$authorized){
            $method_name=$method.'SpecialCase';
            if(method_exists($panel, $method_name)){
                return $panel->$method_name();
            }
            $msg=[
                'err_msg'=>'Not Authorized',
                'row'=>get_class($row),
                'method'=>$method,
                'logged'=>\Auth::check(),
                'panel'=>get_class($panel),
                'policy'=>get_class($policy),
                'special_case'=>$method_name,
                //'special_case_exists'=>'NO',
                'tips'=>'modify policy or create special case',
            ];
            ddd($msg);
            abort(403);    
        }
        if($request->getMethod()!='GET'){
            //ddd($panel->rules());
            $request->validate($panel->rules(),$panel->rulesMessages());
        }
        return app($controller)->$method($request,$this->container_last,$this->item_last);        

    }

    public function __call($method, $args){
        $params = \Route::current()->parameters();
        list($containers,$items)=params2ContainerItem($params);
        $request=Request::capture();
        $a=$this->init($params);
        $controller = $this->controller;
        $row=$this->last;
       // ddd($this->authorize($method,$row));
        if(!is_object($row) && $row!='' && config('xra.model.'.$row)!=''){
            $class=config('xra.model.'.$row);
            if($class==''){
                ddd('['.$row.'] not exists on config/xra.php');
            }
            try{
                $row=new $class;
            }catch(\Exception $e){
                ddd('['.$row.']['.$class.'] not exists on config/xra.php');
            }
        } 
        $panel=StubService::getByModel($row,'panel',$create = true); 
        $policy=StubService::getByModel($row,'policy',$create = true);
        if(\Auth::check()){
            $authorized=\Auth::user()->can($method, $row); 
        }else{
            $authorized=Gate::allows($method, $row);
            //$authorized=\Auth::guest()->can($method, $row); 
        }
        if(!$authorized){
            $method_name=$method.'SpecialCase';
            if(method_exists($panel, $method_name)){
                return $panel->$method_name();
            }
            $msg=[
                'err_msg'=>'Not Authorized',
                'row'=>get_class($row),
                'method'=>$method,
                'logged'=>\Auth::check(),
                'panel'=>get_class($panel),
                'policy'=>get_class($policy),
                'special_case'=>$method_name,
                //'special_case_exists'=>'NO',
                'tips'=>'modify policy or create special case',
            ];
            ddd($msg);
            abort(403);    
        }
        //if(in_array($method,['update','store'])){
        if($request->getMethod()!='GET'){
            //ddd($panel->rules());
            $request->validate($panel->rules(),$panel->rulesMessages());
        }
        return app($controller)->$method($request,$this->container_last,$this->item_last);        
    }


    /*------------
     public function update(?User $user, Post $post) -> https://laravel.com/docs/5.8/authorization

    $request = \str_replace('\\Controllers\\', '\\Requests\\', $controller);
            $request = \mb_substr($request, 0, -\mb_strlen('Controller'));
            $pos = \mb_strrpos($request, '\\');
            $request = \mb_substr($request, 0, $pos + 1).studly_case($method).\mb_substr($request, $pos + 1);
            $request = $request::capture();
            $request->validate($request->rules(), $request->messages());
    -------------*/
}
