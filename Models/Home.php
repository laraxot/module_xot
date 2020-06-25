<?php

namespace Modules\Xot\Models;

//--- TRAITS ---
use Modules\Xot\Models\Traits\WidgetTrait;

//------ ext models---

class Home extends BaseModel {
    use WidgetTrait;
    protected $fillable = ['id', 'article_type', 'icon_src'];
}
