<?php

namespace Modules\Xot\Http\Controllers\Admin;

//use Modules\Xot\Traits\CrudContainerItemNoPostTrait as CrudTrait;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class XotPanelController extends Controller {
    public function __call($name, $arg) {
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
