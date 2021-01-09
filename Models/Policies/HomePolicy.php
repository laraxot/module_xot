<?php

namespace Modules\Xot\Models\Policies;

/**
 * Class HomePolicy
 * @package Modules\Xot\Models\Policies
 */
class HomePolicy extends XotBasePolicy {
    /**
     * @param \Modules\Xot\Contracts\UserContract|null $user
     * @param $post
     * @return bool
     */
    public function index($user, $post) {
        return true; //da aggiungere pezzi
    }

    /**
     * @param \Modules\Xot\Contracts\UserContract|null $user
     * @param $post
     * @return bool
     */
    public function show($user, $post) {
        return true; //da aggiungere pezzi
    }
}
