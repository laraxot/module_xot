<?php

namespace Modules\Xot\Models\Panels\Policies;

/**
 * Class ImagePanelPolicy
 * @package Modules\Xot\Models\Panels\Policies
 */
class ImagePanelPolicy extends XotBasePanelPolicy {
    /**
     * @param \Modules\Xot\Contracts\UserContract $user
     * @param \Modules\Xot\Contracts\PanelContract $panel
     * @return bool
     */
    public function store($user, $panel) {
        return true; //da aggiungere pezzi
    }
}
