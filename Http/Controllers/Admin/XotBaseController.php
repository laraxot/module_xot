<?php

declare(strict_types=1);

namespace Modules\Xot\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Xot\Traits\CrudContainerItemJobTrait as CrudTrait;

//-- jobs --
/*
use Modules\Xot\Jobs\Crud\IndexJob;

use Modules\Xot\Jobs\Crud\editJob;
use Modules\Xot\Jobs\Crud\updateJob;

use Modules\Xot\Jobs\Crud\createJob;
use Modules\Xot\Jobs\Crud\storeJob;
*/

/**
 * Class XotBaseController.
 */
abstract class XotBaseController extends Controller
{
    use CrudTrait;
    /*
    public function __call($name, $arg){
        $func='\Modules\Xot\Jobs\Crud\\'.$name.'Job';
        $panel=$func::dispatchNow($arg[1],$arg[2]);
        return $panel;
    }
    */
}
