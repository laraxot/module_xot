<?php

declare(strict_types=1);

namespace Modules\Xot\Facades;

use Illuminate\Support\Facades\Facade;

class Profile extends Facade {
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'profile';
    }
}
