<?php

namespace Modules\Xot\Models;

use Illuminate\Database\Eloquent\Model;
//use Laravel\Scout\Searchable;
//---- Traits ----
use Modules\Xot\Traits\Updater;

/**
 * Class XotBaseModel.
 */
abstract class XotBaseModel extends Model {
    //use Searchable;
    use Updater;
}