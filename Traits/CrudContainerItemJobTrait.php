<?php

namespace Modules\Xot\Traits;

use Illuminate\Support\Str;

trait CrudContainerItemJobTrait {
    public function __call($name, $arg) {
        $func = '\Modules\Xot\Jobs\Crud\\'.Str::studly($name).'Job';
        $panel = $func::dispatchNow($arg[1], $arg[2]);

        return $panel;
    }
}
