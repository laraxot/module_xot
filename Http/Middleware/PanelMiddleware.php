<?php

namespace Modules\Xot\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Xot\Services\PanelService;

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
        [$containers,$items] = params2ContainerItem($parameters);
        //$obj_containers = [];
        //$obj_items = [];

        //if (count($containers) > count($items)) { //rows
        //}
        //dddx($parameters);
        /*
        $tmp = xotModel($containers[0]);
        $panel = PanelService::get($tmp);
        $panel->setRows($tmp);
        $panel->setItem($items[0]);
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
