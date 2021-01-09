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
    public function index(?\Modules\Xot\Contracts\UserContract $user, $post) {
        return true; //da aggiungere pezzi
    }

    /**
     * @param \Modules\Xot\Contracts\UserContract|null $user
     * @param $post
     * @return bool
     */
    public function show(?\Modules\Xot\Contracts\UserContract $user, $post) {
        return true; //da aggiungere pezzi
    }
}
