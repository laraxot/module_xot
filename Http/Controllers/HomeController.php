<?php

namespace Modules\Xot\Http\Controllers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
//---- services ---

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use Modules\Theme\Services\ThemeService;
use Modules\Xot\Services\PanelService as Panel;
use Modules\Xot\Services\TenantService as Tenant;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        try {
            $home = Tenant::modelEager('home')->firstOrCreate(['id' => 1]);
        } catch (\Exception $e) {
            dddx('run migrations');
        }

        $home_panel = Panel::get($home);

        if ('' != $request->_act) {
            return $home_panel->callItemActionWithGate($request->_act);
        }

        return  ThemeService::view('pub_theme::home.index')
            ->with('home', $home)
            ->with('_panel', $home_panel)
            ;
    }

    public function createHomesTable()
    {
        Schema::create('homes', function (Blueprint $table) {
            $table->increments('id');

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->timestamps();
        });
    }

    public function show(Request $request)
    {
        if ('' != $request->_act) {
            $home = Tenant::model('home');
            $panel = Panel::get($home);
            return $panel->callItemActionWithGate($request->_act);
        }
        try {
            $home = Tenant::modelEager('home')
        ->firstOrCreate(['id' => 1]);
        } catch (\Exception $e) {
            dddx($e);
        }
        $panel = Panel::get($home);
        return $panel->out();
    }

    public function redirect(Request $request)
    {
        return redirect($request->url);
    }
}
