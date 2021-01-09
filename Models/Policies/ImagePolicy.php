<?php

namespace Modules\Xot\Models\Policies;

/**
 * Class ImagePolicy
 * @package Modules\Xot\Models\Policies
 */
class ImagePolicy extends XotBasePolicy {
    /**
     * @param \Modules\Xot\Contracts\UserContract $user
     * @param $post
     * @return bool
     */
    public function store(\Modules\Xot\Contracts\UserContract $user, $post) {
        return true; //da aggiungere pezzi
    }
}
