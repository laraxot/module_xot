<?php

namespace Modules\Xot\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

//---- services ---
use Modules\Theme\Services\ThemeService;
use Modules\Xot\Services\ArtisanService;

use Illuminate\Support\Facades\Bus;

class HomeController extends Controller{
    
    public function index(Request $request){
    	//$command=new \Modules\Xot\Commands\PurchasePodcast(\Auth::user());
    	//Bus::dispatch($command);
    		


        $out=ArtisanService::act($request->act);
        if($out!='') return $out;
        return ThemeService::view('pub_theme::home.index');
    }

    public function redirect(Request $request){
        return redirect($request->url);
    }


}
