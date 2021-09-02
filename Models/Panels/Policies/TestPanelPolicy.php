<?php

declare(strict_types=1);

namespace Modules\Xot\Models\Panels\Policies;

use Modules\Xot\Contracts\PanelContract;
use Modules\Xot\Contracts\UserContract;

class TestPanelPolicy extends XotBasePanelPolicy {
    public function testMail(UserContract $user, PanelContract $panel): bool {
        return true;
    }
}
