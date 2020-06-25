<?php

namespace Modules\Xot\Models;

use Illuminate\Database\Eloquent\Model;

//------ ext models---

class Metatag extends BaseModel {
    protected $fillable=['id','title','subtitle','charset','author','meta_description','meta_keywords','logo_src','logo_footer_src','tennant_name','created_at','created_by','updated_at','updated_by'];


}
