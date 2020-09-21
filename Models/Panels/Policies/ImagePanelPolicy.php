<?php

namespace Modules\Xot\Models\Panels\Policies;

class ImagePanelPolicy extends XotBasePolicy {
    public function store($user, $panel) {
        return true; //da aggiungere pezzi
    }
}
