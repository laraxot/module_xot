<?php

declare(strict_types=1);

namespace Modules\Xot\Actions\Filament;

use Filament\Facades\Filament;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\QueueableAction\QueueableAction;

class GetModuleContexts
{
    use QueueableAction;

    /**
     * Undocumented function.
     */
    public function execute(string $module): Collection
    {
        $prefix = Str::of($module)->lower()->append('-')->toString();

        return collect(Filament::getContexts())
            ->keys()
            ->filter(fn ($item) => Str::of($item)->contains("{$prefix}"));
    }
}
