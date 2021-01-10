<?php

namespace Modules\Xot\Models\Panels\Policies;

/**
 * Class MetatagPanelPolicy
 * @package Modules\Xot\Models\Panels\Policies
 */
class MetatagPanelPolicy extends XotBasePanelPolicy {
    /**
     * @param UserContract $user
     * @param PanelContract $panel
     * @return false
     */
    public function storeFileMetatag(UserContract $user,PanelContract $panel):bool {
        //return ($metatag->tennant_name=='foodlocal');
        return false;
    }
}
