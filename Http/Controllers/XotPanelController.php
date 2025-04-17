<?php

declare(strict_types=1);

namespace Modules\Xot\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

/**
 * Class XotPanelController.
 */
class XotPanelController extends Controller
{
    /**
     * @param string $method
     * @param array  $arg
     */
    public function __call($method, $arg)
    {
        // dddx(['name' => $method, 'arg' => $arg]);
        /*
         * 0 => xotrequest
         * 1 => userPanel.
         */
        /*
        $func = '\Modules\Xot\Jobs\PanelCrud\\'.Str::studly($method).'Job';

        $data = $arg[0];
        if ($arg[0] instanceof Request) {
            $data = $data->all();
        }
        $panel = $func::dispatchNow($data, $arg[1]);

        return $panel->out();
        */
        $act = '\Modules\Cms\Actions\Panel\\'.Str::studly($method).'Action';
        $data = $arg[0];
        if ($arg[0] instanceof Request) {
            $data = $data->all();
        }

        $panel = app($act)->execute($arg[1], $data);

        return $panel->out();
    }
}
