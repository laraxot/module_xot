<?php

declare(strict_types=1);

namespace Modules\Xot\Providers\Traits;

use Illuminate\View\Compilers\BladeCompiler;

trait BladeTrait
{
    /**
     * Register Blade directives.
     *
     * @return void
     */
    protected function registerBladeDirectives()
    {
        $this->app->afterResolving(
            'blade.compiler',
            function (BladeCompiler $bladeCompiler) {
                dddx(['bladeCompiler' => $bladeCompiler]);
            }
        );
    }

    // end registerBladeDirectives
}
