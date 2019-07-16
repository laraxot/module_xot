<?php

namespace Modules\Xot\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

//use Modules\Extend\Traits\CrudContainerItemNoPostTrait as CrudTrait;

use Modules\Extend\Services\ArtisanService;

class ModuleController extends Controller
{

    //use CrudTrait;

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index_old(Request $request){
        /*
        \Artisan::call('route:list');
        $output = '[<pre>'.\Artisan::output().'</pre>]';
        return $output;
        //return view('xot::index');
        */
        $params = \Route::current()->parameters();
        extract($params);
        $mod=\Module::find($module);
        $controller='\Modules\\'.$mod->name.'\\Http\Controllers\Admin\\'.$mod->name.'Controller';
        $controller='\Modules\Xot\Http\Controllers\Admin\XotBaseController';

        $method=__FUNCTION__;
        return app($controller)->$method($request,false,false);
        //ddd($params);
    }


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
        if($method=='index'){
            $out=ArtisanService::act($request->act);
            if($out!='') return $out;
        }
        if (is_object($row) && \Auth::user()->cannot($method, $row)) {
            ddd('non autorizzato');
            abort(403);
        }
        //ddd($controller);
        return app($controller)->$method($request,$this->container_last,$this->item_last);

    }

}
