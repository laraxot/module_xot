<?php

namespace Modules\Xot\Http\Controllers\Admin\Module;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Xot\Http\Controllers\Admin\XotBaseContainerController;

//use Modules\Xot\Traits\CrudContainerItemNoPostTrait as CrudTrait;

class Container0Controller extends XotBaseContainerController {
    /*
    public function getController(){
        $params = \Route::current()->parameters();
        list($containers,$items)=params2ContainerItem($params);
        \extract($params);
        $mod=\Module::find($module);
        $controller='\Modules\\'.$mod->name.'\Http\Controllers\Admin\\'.Str::studly($container0).'Controller';
        return $controller;
    }
    public function __call($method, $args){
        $controller = $this->getController();
        $request=Request::capture();
        return app($controller)->$method($request);
    }
    */
}
