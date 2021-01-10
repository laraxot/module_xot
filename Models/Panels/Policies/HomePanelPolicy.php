<?php

declare(strict_types=1);

namespace Modules\Xot\Models\Panels\Policies;

use Modules\Xot\Contracts\PanelContract;
use Modules\Xot\Contracts\UserContract;

/**
 * Class HomePanelPolicy.
 */
class HomePanelPolicy extends XotBasePanelPolicy {
    public function index(?UserContract $user, PanelContract $panel): bool {
        return true; //da aggiungere pezzi
    }

    public function show(?UserContract $user, PanelContract $panel): bool {
        return true; //da aggiungere pezzi
    }

    public function artisan(UserContract $user, PanelContract $panel): bool {
        return true; //da aggiungere pezzi
    }

    public function test(UserContract $user, PanelContract $panel): bool {
        return true; //da aggiungere pezzi
    }
}
