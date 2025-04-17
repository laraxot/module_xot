<?php

declare(strict_types=1);

namespace Modules\Xot\Actions\Filament;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Blade;
use Spatie\QueueableAction\QueueableAction;

class RenderContextNavigation
{
    use QueueableAction;

    /**
     * Undocumented function.
     */
    public function execute(string $module, string $context): void
    {
        Filament::registerRenderHook(
            'sidebar.start',
            fn (): string => Blade::render('<div class="p-2 px-6 bg-primary-100 font-black w-full">'."{$module} Module</div>")
        );
        Filament::registerRenderHook(
            'sidebar.end',
            fn (): string => Blade::render('<a class="p-2 px-6 bg-primary-100 font-black w-full inline-flex space-x-2" href="'.route('filament.pages.dashboard').'"><x-heroicon-o-arrow-left class="w-5"/> Main Module</a>')
        );

        Filament::registerRenderHook(
            'user-menu.start',
            fn (): string => Blade::render('@livewire(\'switchable-team\')'),
        );
    }
}
