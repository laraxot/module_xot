<?php

namespace Modules\Xot\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Xot\Services\PanelService;

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
            return $next($request);
        }

        $row = xotModel($containers[0]);
        $panel = PanelService::get($row);
        $panel->setRows($row);
        if (isset($items[0])) {
            $panel->setItem($items[0]);
        }
        $panel_parent = $panel;

        for ($i = 1; $i < count($containers); ++$i) {
            $row_prev = $panel_parent->row;
            $types = Str::camel(Str::plural($containers[$i]));
            $rows = $row_prev->{$types}();
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
