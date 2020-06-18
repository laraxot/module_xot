<?php

namespace Modules\Xot\Models;

//--- TRAITS ---
use Modules\Xot\Models\Traits\HomeTrait;

//------ ext models---

class Home extends BaseModel {
    use HomeTrait;
    protected $fillable = ['id', 'article_type', 'icon_src'];
}
