<?php

namespace Modules\Xot\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
//---- services ---
use Modules\Blog\Models\Home;
use Modules\Theme\Services\ThemeService;
use Modules\Xot\Services\PanelService as Panel;

class HomeController extends Controller {
    public function index(Request $request) {
        $home = Home::with('post')
            ->firstOrCreate(['id' => 1]);
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
        //dddx(app()->getLocale());
        $home = Home::with('post')
        ->firstOrCreate(['id' => 1]);

        $panel = Panel::get($home);

        return $panel->out();

        /*
        $lang = app()->getLocale();
        $guid = 'chicken-taste-restaurant';
        if (1) {
            $item = \Modules\Food\Models\Restaurant::with('post')
            ->whereHas('post',
                function ($query) use ($guid,$lang) {
                    $query->where('guid', 'like', $guid)
                        ->where('lang', $lang);
                }
            )->first();
        } else {
            $post = \Modules\Blog\Models\Post::where('guid', 'like', $guid)
            ->where('lang', $lang)->first();
            $item = $post->linkable;
        }
        $panel = Panel::get($item);

        return $panel->out();
        //*/
    }

    public function redirect(Request $request) {
        return redirect($request->url);
    }
}
