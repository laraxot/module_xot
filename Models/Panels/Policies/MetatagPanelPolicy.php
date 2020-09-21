<?php

namespace Modules\Xot\Models\Panels\Policies;

use Modules\Xot\Models\Policies\XotBasePolicy;

class MetatagPanelPolicy extends XotBasePolicy {
    public function storeFileMetatag($user, $panel) {
        //return ($metatag->tennant_name=='foodlocal');
        return false;
    }
}
