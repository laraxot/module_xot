<?php

namespace Modules\Xot\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//--- services
use Modules\Theme\Services\ThemeService;
//--- traits
use Modules\Xot\Services\ArtisanService;
use Modules\Xot\Traits\ArtisanTrait;

class BackendController extends Controller {
    public function index(Request $request) {
        if ('routelist' == $request->act) {
            return ArtisanTrait::exe('route:list');
        }

        return ThemeService::view();
    }

    public function dashboard(Request $request) {
        $out = ArtisanService::act($request->act);
        if ('' != $out) {
            return $out;
        }

        return ThemeService::view('adm_theme::admin.dashboard');
    }
}
