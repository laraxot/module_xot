<?php

namespace Modules\Xot\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
//---- services ---
use Illuminate\Support\Facades\Bus;
use Modules\Theme\Services\ThemeService;
use Modules\Xot\Services\ArtisanService;

class HomeController extends Controller {
    public function index(Request $request) {
        //$command=new \Modules\Xot\Commands\PurchasePodcast(\Auth::user());
        //Bus::dispatch($command);
        $out = ArtisanService::act($request->act);
        if ('' != $out) {
            return $out;
        }

        return ThemeService::view('pub_theme::home.index');
    }

    public function redirect(Request $request) {
        return redirect($request->url);
    }
}
