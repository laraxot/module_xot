<?php

namespace Modules\Xot\Models;

//------ ext models---

class Profile extends BaseModel {
    protected $fillable = ['id', 'auth_user_id'];
}
