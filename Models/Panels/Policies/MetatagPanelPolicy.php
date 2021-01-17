<?php

declare(strict_types=1);

namespace Modules\Xot\Models\Panels\Policies;

use Modules\Xot\Contracts\PanelContract;
use Modules\Xot\Contracts\UserContract;

/**
 * Class MetatagPanelPolicy.
 */
class MetatagPanelPolicy extends XotBasePanelPolicy {
    public function storeFileMetatag(UserContract $user, PanelContract $panel): bool {
        //return ($metatag->tennant_name=='foodlocal');
        return false;
    }
}
