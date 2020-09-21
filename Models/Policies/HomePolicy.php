<?php

namespace Modules\Xot\Models\Policies;

class HomePolicy extends XotBasePolicy {
    public function index($user, $post) {
        return true; //da aggiungere pezzi
    }

    public function show($user, $post) {
        return true; //da aggiungere pezzi
    }
}
