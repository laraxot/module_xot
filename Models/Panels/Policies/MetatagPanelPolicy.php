<?php

namespace Modules\Xot\Models\Panels\Policies;

/**
 * Class MetatagPanelPolicy
 * @package Modules\Xot\Models\Panels\Policies
 */
class MetatagPanelPolicy extends XotBasePanelPolicy {
    /**
     * @param $user
     * @param $panel
     * @return false
     */
    public function storeFileMetatag($user, $panel) {
        //return ($metatag->tennant_name=='foodlocal');
        return false;
    }
}
