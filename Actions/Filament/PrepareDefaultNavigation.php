<?php

declare(strict_types=1);

namespace Modules\Xot\Actions\Filament;

use Filament\Facades\Filament;
use Spatie\QueueableAction\QueueableAction;

class PrepareDefaultNavigation
{
    use QueueableAction;

    /**
     * Undocumented function.
     */
    public function execute(string $module, string $context): void
    {
        Filament::serving(function () use ($module, $context) {
            Filament::forContext('filament', function () use ($module, $context) {
                app(RegisterFilamentNavigationItem::class)->execute($module, $context);
            });
            Filament::forContext($context, function () use ($module, $context) {
                app(RenderContextNavigation::class)->execute($module, $context);
            });
        });
    }
}
