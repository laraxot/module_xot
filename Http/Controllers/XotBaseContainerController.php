<?php

namespace Modules\Xot\Http\Controllers;

use Illuminate\Routing\Controller;
//--- services ---
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Modules\Xot\Http\Requests\XotRequest;
use Modules\Xot\Services\PanelService as Panel;

/**
 * Class XotBaseContainerController
 * @package Modules\Xot\Http\Controllers
 */
abstract class XotBaseContainerController extends Controller {
    /**
     * @var
     */
    protected $panel;

    /**
     * @param string $method
     * @param array $args
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|mixed
     */
    public function __call($method, $args) {
        $panel = Panel::getRequestPanel();
        $this->panel = $panel;

        if ('' != request()->input('_act', '')) {
            return $this->__callPanelAct($method, $args);
        }

        return $this->__callRouteAct($method, $args);
    }

    /**
     * @return string
     */
    public function getController() {
        list($containers, $items) = params2ContainerItem();
        $mod_name = $this->panel->getModuleName(); //forse da mettere container0

        $tmp = collect($containers)->map(
            function ($item) {
                return Str::studly($item);
            }
        )->implode('\\');
        $controller = '\Modules\\'.$mod_name.'\Http\Controllers\\'.$tmp.'Controller';
        if (class_exists($controller)) {
            return $controller;
        }

        return '\Modules\Xot\Http\Controllers\XotPanelController';
    }

    /**
     * @param $method
     * @param $args
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function __callRouteAct($method, $args) {
        $panel = $this->panel;
        //$authorized = Gate::allows($method, $model);
        $authorized = Gate::allows($method, $panel);

        if (! $authorized) {
            return $this->notAuthorized($method, $panel);
        }

        $request = XotRequest::capture();
        $controller = $this->getController();
        $data = $request->all();

        $panel = app($controller)->$method($data, $panel);

        return $panel->out(
            [
                'is_ajax' => $request->ajax(),
                'method' => $request->getMethod(),
            ]
        );
    }

    /**
     * @param $method
     * @param $args
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function __callPanelAct($method, $args) {
        $request = request();
        $act = $request->_act;
        $method_act = Str::camel($act);

        $panel = $this->panel;

        $authorized = Gate::allows($method_act, $panel);
        if (! $authorized) {
            return $this->notAuthorized($method_act, $panel);
        }

        return $panel->callAction($act);
    }

    /**
     * @param $method
     * @param $panel
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function notAuthorized($method, $panel) {
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
        $msg = 'Auth Id ['.\Auth::id().'] not can ['.$method.'] on ['.get_class($panel).']';

        //abort(403, $msg);
        return response()->view('pub_theme::errors.403', ['msg' => $msg], 403);
    }
}
