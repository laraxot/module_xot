<?php

declare(strict_types=1);

namespace Modules\Xot\Providers\Traits;

use Illuminate\Cache\TagSet;
use Illuminate\Support\Facades\Cache;

trait OPCacheTrait
{
    public function registerCacheOPCache(): void
    {
        Cache::extend(
            'opcache',
            function () {
                $store = new \Modules\Xot\Engines\Opcache\Store();

                return new \Modules\Xot\Engines\Opcache\Repository($store, new TagSet($store));
            }
        );
        //
        // Session::extend('opcache', function () {
        //    $store = new \Modules\Xot\Engines\Opcache\Store();

        //    return new \Modules\Xot\Engines\Opcache\Repository($store, new TagSet($store));
        // });

        // Extend Collection to implement __set_state magic method
        if (! Collection::hasMacro('__set_state')) {
            Collection::macro(
                '__set_state',
                function (array $array) {
                    return new Collection($array['items']);
                }
            );
        }
    }
}
