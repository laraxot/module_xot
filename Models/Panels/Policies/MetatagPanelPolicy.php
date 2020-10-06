<?php

namespace Modules\Xot\Models\Panels\Policies;

class MetatagPanelPolicy extends XotBasePanelPolicy {
    public function storeFileMetatag($user, $panel) {
        //return ($metatag->tennant_name=='foodlocal');
        return false;
    }
}
