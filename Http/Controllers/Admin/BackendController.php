<?php

namespace Modules\Xot\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
//--- services
//use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
//--- traits
use Modules\Theme\Services\ThemeService;
use Modules\Xot\Services\ArtisanService;

/**
 * Class BackendController
 * @package Modules\Xot\Http\Controllers\Admin
 */
class BackendController extends Controller {
    /**
     * @param Request $request
     * @return mixed|string
     */
    public function index(Request $request) {
        if ('routelist' == $request->act) {
            return ArtisanService::exe('route:list');
        }

        return ThemeService::view();
    }

    public function dashboard(Request $request): View {
        /*
        $out = ArtisanService::act($request->act);
        if ('' != $out) {
            return $out;
        }
        */
        //return view('adm_theme::admin.dashboard');
        //return ThemeService::view('adm_theme::admin.dashboard');
        return view()->make('adm_theme::admin.dashboard');
    }
}
