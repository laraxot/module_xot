<?php

declare(strict_types=1);

namespace Modules\Xot\Http\Controllers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
//---- services ---

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use Modules\Theme\Services\ThemeService;
use Modules\Xot\Services\PanelService as Panel;
use Modules\Xot\Services\TenantService as Tenant;

/**
 * Class HomeController.
 */
class HomeController extends Controller {
    /**
     * @return mixed
     */
    public function index(Request $request) {
        $home = null;
        try {
            $model = Tenant::modelEager('home');
            $home = $model->firstOrCreate(['id' => 1]);
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

    public function createHomesTable() {
        Schema::create('homes', function (Blueprint $table) {
            $table->increments('id');

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * @return mixed
     */
    public function show(Request $request) {
        $home = null;
        if ('' != $request->_act) {
            $home = Tenant::model('home');
            $panel = Panel::get($home);

            return $panel->callItemActionWithGate($request->_act);
        }
        try {
            $home = Tenant::modelEager('home');
            $home = $home->firstOrCreate(['id' => 1]);
        } catch (\Exception $e) {
            dddx(['exception' => $e, 'model' => $home]);
        }
        $panel = Panel::get($home);

        return $panel->out();
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function redirect(Request $request) {
        return redirect($request->url);
    }
}
