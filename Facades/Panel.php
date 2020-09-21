<?php

namespace Modules\Xot\Facades;

class Panel extends Facade {
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'panel';
    }
}
