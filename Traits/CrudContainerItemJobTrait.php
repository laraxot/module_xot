<?php

namespace Modules\Xot\Traits;

use Illuminate\Support\Str;

/**
 * Trait CrudContainerItemJobTrait
 * @package Modules\Xot\Traits
 */
trait CrudContainerItemJobTrait {
    /**
     * @param $name
     * @param $arg
     * @return mixed
     */
    public function __call($name, $arg) {
        $func = '\Modules\Xot\Jobs\Crud\\'.Str::studly($name).'Job';
        $panel = $func::dispatchNow($arg[1], $arg[2]);

        return $panel;
    }
}
