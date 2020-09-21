<?php

namespace Modules\Xot\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Xot\Services\PanelService;
use Modules\Xot\Services\TenantService;

//use Illuminate\Http\Response;

class PanelMiddleware {
    /*
    public function __construct($params) {
        dddx($params);
    }
    */

    public function handle(Request $request, Closure $next) {
        $parameters = request()->route()->parameters();
        //dddx($parameters);
        /*
        * "module" => "lu"
        * "lang" => "it"
        * "container0" => "user"
        *
        *
        */
        [$containers, $items] = params2ContainerItem($parameters);

        if (0 == count($containers)) {
            /*
            $row = TenantService::model('home');
            $panel = PanelService::get($row);
            PanelService::setRequestPanel($panel);
            */
            PanelService::setRequestPanel(null);

            return $next($request);
        }

        $row = xotModel($containers[0]);
        $panel = PanelService::get($row);
        if (! isset($panel)) {
            abort(404);
        }
        $panel->setRows($row);
        if (isset($items[0])) {
            $panel->setItem($items[0]);
        }
        $panel_parent = $panel;

        for ($i = 1; $i < count($containers); ++$i) {
            $row_prev = $panel_parent->row;
            $types = Str::camel(Str::plural($containers[$i]));
            try {
                $rows = $row_prev->{$types}();
            } catch (\Exception $e) {
                abort(404, $e->getMessage());
            } catch (\Error $e) {
                //return response("User can't perform this action.", 404);
                $data = [
                    'lang' => \App::getLocale(),
                    'params' => $parameters,
                ];

                return response()->view('pub_theme::errors.404', $data, 404);
            }
            $row = $rows->getRelated();

            $panel = PanelService::get($row);
            $panel->setRows($rows);
            $panel->setParent($panel_parent);

            if (isset($items[$i])) {
                $panel->setItem($items[$i]);
            }
            $panel_parent = $panel;
        }
        //$request['panel'] = $panel;
        PanelService::setRequestPanel($panel);

        return $next($request);
    }
}
