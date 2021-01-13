<?php

namespace Modules\Xot\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
//---- services ---
use Modules\Theme\Services\ThemeService;
use Modules\Xot\Services\PanelService;
use Modules\Xot\Services\TenantService;

//use Modules\Xot\Services\ArtisanService;

/**
 * Class HomeController
 * @package Modules\Xot\Http\Controllers\Admin
 */
class HomeController extends Controller {
    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request) {
        //$out = ArtisanService::act($request->act);
        //if ('' != $out) {
        //    return $out;
        //}
        //
        /*
        $home = TenantService::model('home');
        $panel = PanelService::get($home);

        return $panel->view(); //mi restituisce la index delle "homes"
        */
        $params = \Route::current()->parameters();
        $home_view = $params['module'].'::admin.index';
        if (\View::exists($home_view)) {
            return ThemeService::view($home_view);
        }

        return ThemeService::view('xot::admin.home');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function redirect(Request $request) {
        return redirect($request->url);
    }
}
