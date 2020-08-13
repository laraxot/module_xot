<?php

namespace Modules\Xot\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model {
    public $fillable = [
        'id', 'appname', 'description', 'keywords', 'author',
    ];
}
