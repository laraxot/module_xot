<?php

namespace Modules\Xot\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
//---- services ---
use Modules\Theme\Services\ThemeService;
use Modules\Xot\Services\ArtisanService;

class HomeController extends Controller {
    public function index(Request $request) {
        $out = ArtisanService::act($request->act);
        if ('' != $out) {
            return $out;
        }
        $params = \Route::current()->parameters();
        $home_view = $params['module'].'::admin.index';
        if (\View::exists($home_view)) {
            return ThemeService::view($home_view);
        }

        return ThemeService::view('xot::admin.home');
    }

    public function redirect(Request $request) {
        return redirect($request->url);
    }
}
