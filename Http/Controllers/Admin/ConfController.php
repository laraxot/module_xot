<<<<<<< HEAD
<?php

namespace Modules\Xot\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
//--- services
use Illuminate\Support\Facades\Config;
use Modules\Settings\Services\ConfService;
use Modules\Theme\Services\ThemeService;
use Modules\Xot\Services\TenantService;

/**
 * Class ConfController.
 */
class ConfController extends Controller {
    /**
     * @return mixed
     */
    public function index(Request $request) {
        $route_params = \Route::current()->parameters();
        //$confs = Config::all('localhost');
        $tenant_name = TenantService::getName();
        $confs = Config::get($tenant_name);
        $rows = collect($confs)->map(function ($item, $key) use ($route_params) {
            $route_params['item0'] = $key;

            return (object) [
                'title' => $key,
                'url' => route('admin.container0.edit', $route_params, false),
            ];
        })->all();

        return ThemeService::view()
                ->with('rows', $rows)
                //->with('row',$row)
                ;
    }

    public function edit(Request $request): Renderable {
        $route_params = \Route::current()->parameters();
        extract($route_params);
        if (! isset($item0)) {
            dddx(['err' => 'item0 is missing']);
            throw new \Exception('item0 is missing');
            //return;
        }
        $row = config($item0);

        return ThemeService::view()->with('row', $row);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function update(Request $request) {
        $data = $request->all();
        $route_params = \Route::current()->parameters();
        //dddx([$data, $route_params]);
        $data = collect($data)->except(['_token', '_method'])->all();

        extract($route_params);
        if (! isset($item0)) {
            dddx(['err' => 'item0 is missing']);

            return;
        }
        TenantService::saveConfig(['name' => $item0, 'data' => $data]);
        /*
        $data['_token'] = '';
        unset($data['_token']);
        $data['_method'] = '';
        unset($data['_method']);
        $res = ConfService::set([
            'name' => $item0,
            'data' => $data,
            //'msg'=>'['.$config_file.']!',
        ]);
        $msg = 'Aggiornato ['.$res['filename'].'!';
        \Session::flash('status', $msg.' '.\Carbon\Carbon::now());

        return redirect()->back();
        */
        $msg = 'aggiornato';
        \Session::flash('status', $msg.' '.\Carbon\Carbon::now());

        return redirect()->back();
    }
=======
<?php

namespace Modules\Xot\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
//--- services
use Illuminate\Support\Facades\Config;
use Modules\Settings\Services\ConfService;
use Modules\Theme\Services\ThemeService;
use Modules\Xot\Services\TenantService;

/**
 * Class ConfController.
 */
class ConfController extends Controller {
    /**
     * @return mixed
     */
    public function index(Request $request) {
        $route_params = \Route::current()->parameters();
        //$confs = Config::all('localhost');
        $tenant_name = TenantService::getName();
        $confs = Config::get($tenant_name);
        $rows = collect($confs)->map(function ($item, $key) use ($route_params) {
            $route_params['item0'] = $key;

            return (object) [
                'title' => $key,
                'url' => route('admin.container0.edit', $route_params, false),
            ];
        })->all();

        return ThemeService::view()
                ->with('rows', $rows)
                //->with('row',$row)
                ;
    }

    public function edit(Request $request): Renderable {
        $route_params = \Route::current()->parameters();
        extract($route_params);
        if (! isset($item0)) {
            dddx(['err' => 'item0 is missing']);
            throw new \Exception('item0 is missing');
            //return;
        }
        $row = config($item0);

        return ThemeService::view()->with('row', $row);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function update(Request $request) {
        $data = $request->all();
        $route_params = \Route::current()->parameters();
        //dddx([$data, $route_params]);
        $data = collect($data)->except(['_token', '_method'])->all();

        extract($route_params);
        if (! isset($item0)) {
            dddx(['err' => 'item0 is missing']);

            return;
        }
        TenantService::saveConfig(['name' => $item0, 'data' => $data]);
        /*
        $data['_token'] = '';
        unset($data['_token']);
        $data['_method'] = '';
        unset($data['_method']);
        $res = ConfService::set([
            'name' => $item0,
            'data' => $data,
            //'msg'=>'['.$config_file.']!',
        ]);
        $msg = 'Aggiornato ['.$res['filename'].'!';
        \Session::flash('status', $msg.' '.\Carbon\Carbon::now());

        return redirect()->back();
        */
        $msg = 'aggiornato';
        \Session::flash('status', $msg.' '.\Carbon\Carbon::now());

        return redirect()->back();
    }
>>>>>>> 3c97c308c85924a62f31c89c71edfe23450749f0
}