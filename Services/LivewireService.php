<?php

declare(strict_types=1);

namespace Modules\Xot\Services;

use Livewire\Livewire;

/**
 * LivewireService.
 */
class LivewireService
{
    /**
     * Undocumented function.
     */
    public static function registerComponents(string $path, string $namespace, string $prefix = ''): void
    {
        $comps = FileService::getComponents($path, $namespace.'\Http\Livewire', $prefix);
        /*
        if(count($comps)>1){
            dddx([
                'path'=>$path,
                'namespace'=>$namespace,
                'prefix'=>$prefix,
                'comps'=>$comps,
            ]);
        }
        //*/
        foreach ($comps as $comp) {
            Livewire::component($comp->comp_name, $comp->comp_ns);
        }
    }
}
