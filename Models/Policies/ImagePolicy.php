<?php

namespace Modules\Xot\Models\Policies;

use Modules\LU\Models\User as User;

class ImagePolicy extends XotBasePolicy {
    public function store($user, $post) {
        return true; //da aggiungere pezzi
    }
}
