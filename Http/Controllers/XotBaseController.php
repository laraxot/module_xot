<?php

namespace Modules\Xot\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

//use Modules\Xot\Traits\CrudContainerItemRepositoryTrait as CrudTrait;

abstract class XotBaseController extends Controller
{
    //use CrudTrait;

    public function __call($name, $arg)
    {
        $func  = '\Modules\Xot\Jobs\Crud\\' . Str::kebab($name) . 'Job';
        $panel = $func::dispatchNow($arg[1], $arg[2]);
        return $panel;
    }
}
