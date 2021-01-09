<?php

namespace Modules\Xot\Models\Panels\Policies;

/**
 * Class HomePanelPolicy
 * @package Modules\Xot\Models\Panels\Policies
 */
class HomePanelPolicy extends XotBasePanelPolicy {
    /**
     * @param \Modules\Xot\Contracts\UserContract|null $user
     * @param \Modules\Xot\Contracts\PanelContract $post
     * @return bool
     */
    public function index(?\Modules\Xot\Contracts\UserContract $user, \Modules\Xot\Contracts\PanelContract $post) {
        return true; //da aggiungere pezzi
    }

    /**
     * @param \Modules\Xot\Contracts\UserContract|null $user
     * @param \Modules\Xot\Contracts\PanelContract $post
     * @return bool
     */
    public function show(?\Modules\Xot\Contracts\UserContract $user, \Modules\Xot\Contracts\PanelContract $post) {
        return true; //da aggiungere pezzi
    }

    /**
     * @param $user
     * @param $post
     * @return bool
     */
    public function artisan($user, $post) {
        return true; //da aggiungere pezzi
    }

    /**
     * @param $user
     * @param $post
     * @return bool
     */
    public function test($user, $post) {
        return true; //da aggiungere pezzi
    }
}
