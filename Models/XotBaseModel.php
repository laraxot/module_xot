<?php
namespace Modules\Xot\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

//---- Traits ----
use Modules\Extend\Traits\Updater;

abstract class XotBaseModel extends Model
{
    //use Searchable;
    use Updater;
}
