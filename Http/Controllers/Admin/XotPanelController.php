<?php

declare(strict_types=1);

namespace Modules\Xot\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

/**
 * Class XotPanelController.
 */
class XotPanelController extends Controller {
    /**
     * @param string $method
     * @param array  $arg
     *
     * @return mixed
     */
    public function __call($method, $arg) {
        //dddx(['name' => $name, 'arg' => $arg]);
        /**
         * 0 => xotrequest
         * 1 => userPanel.
         */
        $func = '\Modules\Xot\Jobs\PanelCrud\\'.Str::studly($method).'Job';
        $panel = $func::dispatchNow($arg[0], $arg[1]);

        return $panel;
    }
}
