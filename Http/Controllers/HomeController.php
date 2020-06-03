<?php

namespace Modules\Xot\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
//---- services ---
//use Modules\Blog\Models\Home;
use Modules\Theme\Services\ThemeService;
use Modules\Xot\Services\PanelService as Panel;
use Modules\Xot\Services\TenantService as Tenant;

class HomeController extends Controller {
    public function index(Request $request) {
        $home =Tenant::modelEager('home')->firstOrCreate(['id' => 1]);
        /*
        $home = Home::with('post')
            ->firstOrCreate(['id' => 1]);
            */
        $home_panel = Panel::get($home);

        if ('' != $request->_act) {
            return $home_panel->callItemActionWithGate($request->_act);
        }

        return  ThemeService::view('pub_theme::home.index')
            ->with('home', $home)
            ->with('_panel', $home_panel)
            ;
    }

    public function show(Request $request) {
        $home = Tenant::modelEager('home')
        ->firstOrCreate(['id' => 1]);
        $panel = Panel::get($home);
        return $panel->out();
    }

    public function redirect(Request $request) {
        return redirect($request->url);
    }
}
