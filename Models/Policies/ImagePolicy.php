<?php

namespace Modules\Xot\Models\Policies;

class ImagePolicy extends XotBasePolicy {
    public function store($user, $post) {
        return true; //da aggiungere pezzi
    }
}
