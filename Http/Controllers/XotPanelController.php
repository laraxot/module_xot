<?php

namespace Modules\Xot\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

/**
 * Class XotPanelController
 * @package Modules\Xot\Http\Controllers
 */
class XotPanelController extends Controller {
    /**
     * @param string $name
     * @param array $arg
     * @return mixed
     */
    public function __call(string $name, array $arg) {
        //dddx(['name' => $name, 'arg' => $arg]);
        /**
         * 0 => xotrequest
         * 1 => userPanel.
         */
        $func = '\Modules\Xot\Jobs\PanelCrud\\'.Str::studly($name).'Job';
        $panel = $func::dispatchNow($arg[0], $arg[1]);

        return $panel;
    }
}
