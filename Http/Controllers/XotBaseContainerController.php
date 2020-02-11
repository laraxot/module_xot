<?php

namespace Modules\Xot\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Modules\Theme\Services\ThemeService; //4 guestPolicy
use Modules\Xot\Services\PanelService as Panel;
use Modules\Xot\Services\StubService;

//use Modules\Xot\Traits\CrudContainerItemNoPostTrait as CrudTrait;

abstract class XotBaseContainerController extends Controller
{
    protected $controller;
    protected $row;
    protected $module;
    protected $controller_exist;

    //public function __construct() { //o lo chiamavo "init".. etc etc
    public function init($params)
    { //o lo chiamavo "init".. etc etc
        //$params = \Route::current()->parameters();
        //ddd($params);
        list($containers, $items) = params2ContainerItem($params);
        $tmp = collect($containers)->map(function ($item) {
            return Str::studly($item);
        })->implode('\\');

        $container_first = Arr::first($containers);
        //ddd($container_first); //restaurant
        $model_name = config('xra.model.'.$container_first); // ddd($model_name); //Modules\Food\Models\Restaurant
        $pos = strpos($model_name, '\\Models\\');
        $mod = substr($model_name, 0, $pos);
        //$controller='\Modules\\'.$mod->name.'\Http\Controllers\\'.$tmp.'Controller';
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

        $this->item_last = last($items);
        $this->container_last = last($containers);
        $this->last = last($params);

        return 'init';
    }

    public function __call($method, $args)
    {
        $params = \Route::current()->parameters();
        $lang = \App::getLocale();
        list($containers, $items) = params2ContainerItem($params);
        $request = \Modules\Xot\Http\Requests\XotRequest::capture();
        //$request = Request::capture();
        $a = $this->init($params);
        $controller = $this->controller;
        $row = $this->last;
        // ddd($this->authorize($method,$row));
        if (! is_object($row) && '' != $row && '' != config('xra.model.'.$row)) {
            $class = config('xra.model.'.$row);
            if ('' == $class) {
                ddd('['.$row.'] not exists on config/xra.php');
            }
            try {
                $row = new $class();
            } catch (\Exception $e) {
                ddd('['.$row.']['.$class.'] not exists on config/xra.php');
            }
        }
        $authorized = true;
        if (is_object($row)) {
            $panel = StubService::getByModel($row, 'panel', $create = true);
            $policy = StubService::getByModel($row, 'policy', $create = true);
            //if (\Auth::check()) {
            //    $authorized = \Auth::user()->can($method, $row);
            //} else {
            $authorized = Gate::allows($method, $row);
        }else{
           // ddd($this->controller);
        }
        //$authorized=\Auth::guest()->can($method, $row);
        //}
        if (! $authorized && ! \Auth::check()) {
            $msg = ['msg' => 'ok', 'html' => '<h3>Before Login </h3><button class="btn btn-social btn-facebook" onclick="location.href=\''.url($lang.'/login/facebook').'\';"><i class="fab fa-facebook-square fa-3x  "></i></button>'];
            if ($request->ajax()) {
                return response()->json($msg, 200);
            }
            $referer = url()->current();
            $referer = \Request::path();

            return redirect()->route('login', ['lang' => \App::getLocale(), 'referer' => $referer])
                            ->withErrors(['active' => 'login before']);
        }

        if (! $authorized) {
            $method_name = $method.'SpecialCase';
            if (method_exists($panel, $method_name)) {
                return $panel->$method_name();
            }
            $row_name = is_object($row) ? get_class($row) : $row;
            $panel_name = is_object($panel) ? get_class($panel) : '---';
            $policy_name = is_object($policy) ? get_class($policy) : '---';
            $msg = [
                'err_msg' => 'Not Authorized',
                'row' => $row_name,
                'method' => $method,
                'logged' => \Auth::check(),
                'panel' => $panel_name,
                'policy' => $policy_name,
                'special_case' => $method_name,
                //'special_case_exists'=>'NO',
                'tips' => 'modify policy or create special case',
            ];
            ddd($msg);
            abort(403);
        }
        //if(in_array($method,['update','store'])){
        if ('GET' != $request->getMethod()) {
            if (! $request->ajax()) {
                $route_action = \Route::currentRouteAction();
                $act = Str::after($route_action, '@');
                //$rules=$panel->rules(['act'=>$act]);
                /* -- questo funziona..
                $request->merge(['published_at'=>\Carbon\Carbon::now()]);
                */
                //$request->validate($rules, $panel->rulesMessages());
                $request->validatePanel($panel, $act);
            }
        }
        //return app($controller)->$method($request, $this->container_last, $this->item_last);
        $controller_single = (substr($controller, 0, -strlen('Controller')).'\\'.Str::studly($method).'Controller');
        /*-- to do --
            non passare piu' request ma passare direttamente $data
        */
        //  \Debugbar::disable();
        $panel = app($controller)->$method($request, $this->container_last, $this->item_last);

        return $panel->out(
            [
                'is_ajax' => $request->ajax(),
                'method' => $request->getMethod(),
            ]
        );
        /*
        $row=app($controller)->$method($request, $this->container_last, $this->item_last);
        $rows=app($controller)::$rows;
        $panel=Panel::get($row);
        $html=ThemeService::view(['panel' => $panel, 'row' => $row])
                ->with('row', $row)
                ->with('rows', $rows)
                ->with('_panel', $panel)
                ;
        if ('GET' == $request->getMethod()) {
            return $html;
        }

        if ($request->ajax()) {
            return json_encode(['msg' => 'ok','html'=>(string)$html]);
        } else {
            return $html;
        }
        */
    }

    /*------------
     public function update(?User $user, Post $post) -> https://laravel.com/docs/5.8/authorization

    $request = \str_replace('\\Controllers\\', '\\Requests\\', $controller);
            $request = \mb_substr($request, 0, -\mb_strlen('Controller'));
            $pos = \mb_strrpos($request, '\\');
            $request = \mb_substr($request, 0, $pos + 1).Str::studly($method).\mb_substr($request, $pos + 1);
            $request = $request::capture();
            $request->validate($request->rules(), $request->messages());
    -------------*/
}
