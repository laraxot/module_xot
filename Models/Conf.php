<?php

namespace Modules\Xot\Models;

class Conf extends BaseModel {
    //public $table = '';

    public $fillable = [
        'id', 'appname', 'description', 'keywords', 'author',
    ];
}
