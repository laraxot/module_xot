<?php

namespace Modules\Xot\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Xot\Services\PanelService;
use Illuminate\Support\Str;

//use Illuminate\Http\Response;

class PanelMiddleware
{
    /*
    public function __construct($params) {
        dddx($params);
    }
    */

    public function handle(Request $request, Closure $next)
    {
        $parameters = request()->route()->parameters();
        /*
        * "module" => "lu"
        * "lang" => "it"
        * "container0" => "user"
        * dddx($parameters);
        *
        */
        [$containers, $items] = params2ContainerItem($parameters);
        //$obj_containers = [];
        //$obj_items = [];

        //if (count($containers) > count($items)) { //rows
        //}
        //dddx($parameters);
        /*
        $row = xotModel($containers[0]);
        $panel = PanelService::get($row);
        $panel->setRows($row)->initRows();
        $panel->setItem($items[0]);
        $panel_parent = $panel;
        $row = $panel->row;
        $types = Str::camel(Str::plural($containers[1]));
        $rows = $row->{$types}();
        $row =  $rows->getRelated();
        $panel = PanelService::get($row);
        $panel->setRows($rows)->initRows();
        $panel->setParent($panel_parent);

        $request['panel'] = $panel;

        //*/
        /*
        //$obj_containers[] = xotModel($containers[0]);
        for ($i = 1; $i < count($containers); ++$i) {
            //$obj_items[$i-1]=
            dddx('non dovrei essere qui');
        }

        dddx($panel);
        */

        return $next($request);
    }
}
