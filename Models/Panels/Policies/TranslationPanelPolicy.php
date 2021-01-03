<?php

namespace Modules\Xot\Models\Panels\Policies;

use Modules\Xot\Contracts\PanelContract;
use Modules\Xot\Contracts\UserContract;

class TranslationPanelPolicy extends XotBasePanelPolicy {
    public function clearDuplicatesTrans(UserContract $user, PanelContract $panel) {
        return true;
    }
}
