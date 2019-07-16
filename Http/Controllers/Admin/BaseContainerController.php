<?php

namespace Modules\Xot\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use Illuminate\Support\Str;

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
        if(!isset($params['module'])) return ;
        $mod=\Module::find($params['module']);
        $controller='\Modules\\'.$mod->name.'\Http\Controllers\Admin\\'.$tmp.'Controller';
        $file=$mod->getPath().'\Http\Controllers\Admin\\'.$tmp.'Controller.php';
        $file=str_replace('/',DIRECTORY_SEPARATOR,$file);
        $file=str_replace('\\',DIRECTORY_SEPARATOR,$file);
        if(\File::exists($file)){
            //ddd('esiste ['.$file.']');
            $this->controller=$controller;
        }else{
            //ddd('non esiste ['.$file.']');
            $controller='\Modules\Xot\Http\Controllers\Admin\XotBaseController';
            $this->controller=$controller;
        }
        $this->item_last=last($items);
        $this->container_last=last($containers);
        $this->last=last($params);
    }

	public function __call($method, $args){
        $params = \Route::current()->parameters();
        $this->init($params);
        $controller = $this->controller;
        $row=$this->last;
        $request=Request::capture();
        if (is_object($row) && \Auth::user()->cannot($method, $row)) {
            ddd('non autorizzato ['.$method.']['.get_class($row).']');
            abort(403);
        }
        //ddd($controller);
        return app($controller)->$method($request,$this->container_last,$this->item_last);

    }
}
