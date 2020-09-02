<?php

namespace Modules\Xot\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
//--- services ---
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
    public function init($params) {
        list($containers, $items) = params2ContainerItem($params);
        $tmp = collect($containers)->map(
            function ($item) {
                return Str::studly($item);
            }
        )->implode('\\');

        $container_first = Arr::first($containers);

        $model_name = config('xra.model.'.$container_first);
        $pos = strpos($model_name, '\\Models\\');
        $mod = substr($model_name, 0, $pos);

        $controller = $mod.'\Http\Controllers\\'.$tmp.'Controller';

        try {
            if (class_exists($controller)) {
                $this->controller = $controller;
            } else {
                $controller = '\Modules\Xot\Http\Controllers\XotController';
                $this->controller = $controller;
            }
        } catch (\Exception $e) {
            $controller = '\Modules\Xot\Http\Controllers\XotController';
            $this->controller = $controller;
        }
        $this->items = $items;
        $this->containers = $containers;

        $this->item_last = last($items);
        $this->container_last = last($containers);
        $this->last = last($params);

        return 'init';
    }

    public function notAuthorized($method, $model) {
        $lang = app()->getLocale();
        if (! \Auth::check()) {
            //$request = \Modules\Xot\Http\Requests\XotRequest::capture();
            $request = request();
            if ($request->ajax()) {
                $html = '<h3>Before Login </h3>
            <button class="btn btn-social btn-facebook" onclick="location.href=\''.url($lang.'/login/facebook').'\'">
                <i class="fab fa-facebook-square fa-3x  "></i>
            </button>';
                $msg = ['msg' => 'ok', 'html' => $html];

                return response()->json($msg, 200);
            }

            $referer = \Request::path();

            return redirect()->route('login.notice', ['lang' => $lang, 'referer' => $referer])
            ->withErrors(['active' => 'login before']);
        }
        $msg = 'Auth Id ['.\Auth::id().'] not can ['.$method.'] on ['.get_class($model).']';

        return abort(403, $msg);
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
            if (! is_object($item_last)) {
                abort(404);
            }
            exit(''.get_class($item_last).'->'.$method.'() NOT EXISTS');
        }
        $related = $item_last->$method()->getRelated();

        return $related;
    }

    public function __callPanelAct($method, $args) {
        //$request = \Modules\Xot\Http\Requests\XotRequest::capture();
        $request = request();
        $act = $request->_act;
        $method_act = Str::camel($act);
        $model = $this->getModel();

        $authorized = Gate::allows($method_act, $model);
        if (! $authorized) {
            return $this->notAuthorized($method_act, $model);
        }

        $panel = Panel::get($model);

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
            //dddx($model);
            abort(404);
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
            //ddd($msg);

            return $this->notAuthorized($method, $model);
        }
        /**  se esiste il controller deve eseguire quello
         *   se non esiste il controller da valutare se lasciare controller "generico" o eseguire "panel".
         *
         **/
        $panel = app($this->controller)
            ->$method($request, $this->containers, $this->items);

        return $panel->out(
            [
                'is_ajax' => $request->ajax(),
                'method' => $request->getMethod(),
            ]
        );
    }

    public function __call($method, $args) {
        $params = \Route::current()->parameters();
        //$request = \Modules\Xot\Http\Requests\XotRequest::capture();

        //$url=url()->full();

        //if($url!='http://food.local/it/restaurant/ristorante-1'){
        //    dddx([$url,$request->all(),request()->all(),$_GET]);
        //}

        $a = $this->init($params);

        if ('' != request()->input('_act', '')) {
            return $this->__callPanelAct($method, $args);
        }

        return $this->__callRouteAct($method, $args);
    }
}
