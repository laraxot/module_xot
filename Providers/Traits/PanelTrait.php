<?php

declare(strict_types=1);

namespace Modules\Xot\Providers\Traits;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\URL;

// use Modules\Xot\Engines\FullTextSearchEngine;

trait PanelTrait
{
    private function registerPanel(): void
    {
        // dddx(get_class_methods($this->app['request']));
        // dddx(get_class_methods($this->app['route']));
        // dddx(request()->route()->paremeters());
        // $request->route()->parameters()
        // {{ URL::toCurrentRouteWithParameters(['language' => 'az']) }}
        // dddx(optional(\Route::current())->parameters());
        // dddx(request()->route()->parameters());
        /*
        $this->app->singleton(
            PanelService::class,
            function (Container $app) {
                return new Panel(
                    $app['events'],
                    $app['route'],
                    $app
                );
            }
        );
        */
    }
}
