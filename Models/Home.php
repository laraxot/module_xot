<?php

namespace Modules\Xot\Models;

use Illuminate\Database\Eloquent\Model;

//------ ext models---

class Home extends Model {
    protected $fillable = ['id', 'article_type', 'icon_src'];

    //--------- relationship ---------------
    public function widgets() {
        return $this->morphMany(Widget::class, 'post')->orderBy('pos');
    }
}
