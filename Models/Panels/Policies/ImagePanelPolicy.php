<?php

namespace Modules\Xot\Models\Panels\Policies;

class ImagePanelPolicy extends XotBasePanelPolicy {
    public function store($user, $panel) {
        return true; //da aggiungere pezzi
    }
}
