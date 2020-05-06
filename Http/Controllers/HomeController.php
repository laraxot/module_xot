<?php

namespace Modules\Xot\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
//---- services ---
use Modules\Blog\Models\Home;
use Modules\Theme\Services\ThemeService;
use Modules\Xot\Services\ArtisanService;
use Modules\Xot\Services\PanelService as Panel;

class HomeController extends Controller {
    public function index(Request $request) {
        /*
        $out = ArtisanService::act($request->act);
        if ('' != $out) {
            return $out;
        }
        */
        //$lang = \App::getLocale();
        $home = Home::with('post')
            ->firstOrCreate(['id' => 1]);
        $home_panel = Panel::get($home);

        if ('' != $request->_act) {
            return $home_panel->callItemActionWithGate($request->_act);
        }

        return ThemeService::view('pub_theme::home.index')
            ->with('home', $home)
            ->with('_panel', $home_panel)
            ;
    }

    public function redirect(Request $request) {
        return redirect($request->url);
    }
}
