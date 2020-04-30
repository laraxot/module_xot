<?php

namespace Modules\Xot\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
//---- services ---
use Illuminate\Support\Facades\Bus;
use Modules\Theme\Services\ThemeService;
use Modules\Xot\Services\ArtisanService;
use Modules\Xot\Services\PanelService as Panel;


use Modules\Blog\Models\Home;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        //$command=new \Modules\Xot\Commands\PurchasePodcast(\Auth::user());
        //Bus::dispatch($command);
        $out = ArtisanService::act($request->act);
        if ('' != $out) {
            return $out;
        }
        $lang=\App::getLocale();
        $home=Home::with('post')->firstOrCreate(['id'=>1]);

        //$pathToFile=ThemeService::view_path('pub_theme::img/photo/test.jpg');
        //return '['.$pathToFile.']';
        /*
        $res=$home->addMedia($pathToFile)
        ->preservingOriginal()
        ->toMediaCollection('images')
        //*/
        /*

        */
        ;
        //ddd($res);

        //*/
        //return $home->getFirstMediaUrl();




        return ThemeService::view('pub_theme::home.index')
            ->with('home', $home)->with('_panel', Panel::get($home));
    }

    public function redirect(Request $request)
    {
        return redirect($request->url);
    }
}
