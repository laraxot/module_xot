<?php

namespace Modules\Xot\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

//---- services ---
use Modules\Theme\Services\ThemeService;
use Modules\Extend\Services\ArtisanService;


class HomeController extends Controller{
    
    public function index(Request $request){
        $out=ArtisanService::act($request->act);
        if($out!='') return $out;
        return ThemeService::view('pub_theme::home.index');
    }

    public function redirect(Request $request){
        return redirect($request->url);
    }


}
