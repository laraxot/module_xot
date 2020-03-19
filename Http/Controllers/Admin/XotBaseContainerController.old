<?php

namespace Modules\Xot\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
//---- services ---
use Modules\Xot\Services\PanelService as Panel;
use Modules\Xot\Services\StubService;

//use Modules\Xot\Traits\CrudContainerItemNoPostTrait as CrudTrait;

abstract class XotBaseContainerController extends Controller {
    protected $controller;
    protected $row;
    protected $module;
    protected $controller_exist;

    //public function __construct() { //o lo chiamavo "init".. etc etc
    public function init($params) { //o lo chiamavo "init".. etc etc
        //$params = \Route::current()->parameters();
        //ddd($params);
        list($containers, $items) = params2ContainerItem($params);
        $tmp = collect($containers)->map(function ($item) {
            return Str::studly($item);
        })->implode('\\');
        if (! isset($params['module'])) {
            return;
        }
        $mod = \Module::find($params['module']);
        if (is_object($mod)) {
            $mod_name = $mod->getName();
        }

        if (null == $mod) {
            if (Str::startsWith($params['module'], 'trasferte')) { //CASO ECCEZZIONALE DA GESTIRE DIVERSAMENTE
                $mod = (object) ['name' => 'Trasferte'];
                $mod_name = 'Trasferte';
            }
        }
        $controller = '\Modules\\'.$mod_name.'\Http\Controllers\Admin\\'.$tmp.'Controller';
        //ddd($controller);
        try {
            if (class_exists($controller)) {
                $this->controller = $controller;
            } else {
                $controller = '\Modules\Xot\Http\Controllers\Admin\XotController';
                $this->controller = $controller;
            }
        } catch (\Exception $e) {
            $controller = '\Modules\Xot\Http\Controllers\Admin\XotController';
            $this->controller = $controller;
        }
        $this->item_last = last($items);
        $this->container_last = last($containers);
        $this->last = last($params);
    }

    public function __call($method, $args) {
        $params = \Route::current()->parameters();
        $this->init($params);
        $controller = $this->controller;
        $row = $this->last;
        if (is_object($row) && \Auth::user()->cannot($method, $row)) {
            //ddd('non autorizzato ['.$method.']['.get_class($row).']');
            //return response()->deny('testxxx');
            $msg = 'user ['.\Auth::user()->handle.'] not authorized to ['.$method.'] on class ['.get_class($row).']';
            abort(403, $msg);
            //return response()->view('adm_theme::errors.403', [], 403);
        }
        //$request = Request::capture();
        $request = \Modules\Xot\Http\Requests\XotRequest::capture();
        if (in_array($method, ['update'])) {
            $model = $this->item_last;
            $panel = StubService::getByModel($model, 'panel', $create = true);

            if (is_object($panel)) {
                //$request->prepareForValidation();
                //$request->validate($panel->rules(), $panel->rulesMessages());
                $request->validatePanel($panel);
            }
        }
        if (false == $this->container_last) {
            return app('\Modules\Xot\Http\Controllers\Admin\HomeController')->$method($request);
        }
        //ddd(['controller'=>$controller,'method'=>$method,'container_last'=>$this->container_last]);

        if ('' != $request->_act) {
            $panel = $this->ContainerItem2Panel($this->container_last, $this->item_last);
        } else {
            $panel = app($controller)->$method($request, $this->container_last, $this->item_last);
        }


        return $panel->out(
            [
                'is_ajax' => $request->ajax(),
                'method' => $request->getMethod(),
            ]
        );
    }


    public function ContainerItem2Panel($container, $item) {
        list($containers, $items) = params2ContainerItem();
        if (count($containers) > count($items)) {
            $types = Str::camel(Str::plural($container));
            if (is_object($item)) {
                if (method_exists($item, $types)) {
                    $rows = $item->$types();
                    $related = $rows->getRelated();
                    $row = $related;
                } else {
                    $rows = null;
                    $row = $item;
                }
            } else {
                $row = xotModel($container);
                $rows = $row; // o NULL ???
            }
            $panel = Panel::get($row);
            //$panel->setRow($row);
            $panel->setRows($rows);

            return $panel;
        }
        $panel = Panel::get($item);

        return $panel;
    }
}//end class
