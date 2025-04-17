<?php

declare(strict_types=1);

namespace Modules\Xot\Services;

use Illuminate\Support\Facades\Blade;

/**
 * BladeService.
 */
class BladeService
{
    /**
     * Undocumented function.
     */
    public static function registerComponents(string $path, string $namespace, string $prefix = ''): void
    {
        $comps = FileService::getComponents($path, $namespace.'\View\Components', $prefix);
        foreach ($comps as $comp) {
            Blade::component($comp->comp_name, $comp->comp_ns);
        }
    }
}
