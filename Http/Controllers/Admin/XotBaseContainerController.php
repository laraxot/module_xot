<?php

namespace Modules\Xot\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
//---- services ---
use Modules\Xot\Services\PanelService as Panel;
use Modules\Xot\Services\StubService;
use Modules\Xot\Services\TenantService as Tenant;

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
        $this->items = $items;
        $this->containers = $containers;
        $this->item_last = last($items);
        $this->container_last = last($containers);
        $this->last = last($params);
        if (1 == count($containers) && 0 == count($items)) {
            $models = getModuleModels($mod_name);
            if (isset($models[$this->last])) {
                $class = $models[$this->last];
                $this->last = app($class);
            }
            /*
            dddx([config('xra.model.'.$this->last),
                $mod_name,
                getModuleModels($mod_name),
            ]);
            */
        }
    }

    public function getModel() {
        $params = \Route::current()->parameters();
        [$containers, $items] = params2ContainerItem($params);
        if (0 == count($containers)) {
            return Tenant::model('home');
        }
        if (0 == count($items)) { // es /it/article
            return Tenant::model(last($containers));
        }
        if (count($items) == count($containers)) {
            return last($items);
        }

        $item_last = last($items);
        $container_last = last($containers);
        /**
         * da capire se usare il plurale o meno.
         **/
        $method = Str::camel($container_last);
        if ($plural = 1) { //mi serve per capirmi, equivalenza sempre vera
            $method = Str::plural($method);
        }
        if (! method_exists($item_last, $method)) {
            exit(''.get_class($item_last).'->'.$method.'() NOT EXISTS');
        }
        $related = $item_last->$method()->getRelated();

        return $related;
    }

    public function __callPanelAct($act, $method, $args) {
        $request = \Modules\Xot\Http\Requests\XotRequest::capture();
        $act = $request->_act;
        $method_act = Str::camel($act);
        $model = $this->getModel();
        //dddx($model);

        $authorized = Gate::allows($method_act, $model);
        if (! $authorized) {
            return $this->notAuthorized($method_act, $model);
        }

        //$panel = Panel::get($model);
        $panel = $this->ContainerItem2Panel($this->container_last, $this->item_last);

        return $panel->callAction($act);
        /*
        return $panel->out(
            [
                'is_ajax' => $request->ajax(),
                'method' => $request->getMethod(),
            ]
        );
        */
    }

    //end call panel act

    public function __callRouteAct($method, $args) {
        $request = \Modules\Xot\Http\Requests\XotRequest::capture();
        $model = $this->getModel();
        if (! is_object($model)) {
            dddx($model);
        }

        $authorized = Gate::allows($method, $model);
        if (! $authorized) {
            $policy_class = StubService::fromModel(
                [
                    'model' => $model,
                    'stub' => 'policy',
                ]
            );
            $msg = [
                'model' => $model,
                'policy_class' => $policy_class,
                //'policy_res'=>app($policy_class)->$
                'model_class' => get_class($model),
                'method' => $method,
            ];
            ddd($msg);

            return $this->notAuthorized($method, $model);
        }

        $panel = app($this->controller)
            ->$method($request, $this->containers, $this->items);

        return $panel->out(
            [
                'is_ajax' => $request->ajax(),
                'method' => $request->getMethod(),
            ]
        );
    }

    public function __callUFF($method, $args) {
        $params = \Route::current()->parameters();
        $request = \Modules\Xot\Http\Requests\XotRequest::capture();
        $a = $this->init($params);
        $act = $request->_act;
        if ('' != $act) {
            return $this->__callPanelAct($act, $method, $args);
        }

        return $this->__callRouteAct($method, $args);
    }

    public function __call($method, $args) {
        if (config('xra.notUsePanelMiddleware')) {
            return $this->__call_old_but_working($method, $args);
        }

        return $this->__call_new_not_working_now($method, $args);
    }

    public function __call_new_not_working_now($method, $args) {
        $params = \Route::current()->parameters();
        //$panel = request()->panel;
        $panel = Panel::getRequestPanel();

        if (null == $panel) {
            $request = \Modules\Xot\Http\Requests\XotRequest::capture();

            return app('\Modules\Xot\Http\Controllers\Admin\HomeController')->$method($request);
        }

        //dddx(['params'=>$params,'method'=>$method,'args'=>$args,'panel'=>$panel]);
        $row = $panel->row;
        if (is_object($row) && \Auth::user()->cannot($method, $row)) {
            $msg = 'user ['.\Auth::user()->handle.'] not authorized to ['.$method.'] on class ['.get_class($row).']';
            abort(403, $msg);
        }
        $request = \Modules\Xot\Http\Requests\XotRequest::capture();
        $controller = '\Modules\Xot\Http\Controllers\Admin\XotPanelController';
        $panel = app($controller)->$method($request, $panel);

        if (method_exists($panel, 'out')) {
            return $panel->out(
                [
                    'is_ajax' => $request->ajax(),
                    'method' => $request->getMethod(),
                ]
            );
        }

        return $panel;
    }

    public function __call_old_but_working($method, $args) {
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
            $panel = app($controller)->$method($request, $this->containers, $this->items);
        }

        if (method_exists($panel, 'out')) {
            return $panel->out(
                [
                    'is_ajax' => $request->ajax(),
                    'method' => $request->getMethod(),
                ]
            );
        }

        return $panel;
        /*
        if ($panel instanceof \Illuminate\View\View) {
            return $panel;
        }
        */
        //ddx(['panel' => $panel]);
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
