<?php

declare(strict_types=1);

namespace Modules\Xot\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Modules\Xot\Actions\Filament\PrepareDefaultNavigation;
use Savannabits\FilamentModules\ContextServiceProvider;
use Savannabits\FilamentModules\FilamentModules;

class XotBaseContextServiceProvider extends ContextServiceProvider
{
    public static string $name = 'xot-filament';
    public static string $module = 'Xot';

    public function packageRegistered(): void
    {
        $this->app->booting(function () {
            $this->registerConfigs();
        });
        parent::packageRegistered();
    }

    public function registerConfigs(): void
    {
        $this->mergeConfigFrom(
            app('modules')->findOrFail(static::$module)->getExtraPath('Config/'.static::$name.'.php'),
            static::$name
        );
    }

    public function boot(): void
    {
        parent::boot();
        // app(FilamentModules::class)->prepareDefaultNavigation(static::$module, static::$name);

        app(PrepareDefaultNavigation::class)->execute(static::$module, static::$name);
    }

    /*
    public function prepareDefaultNavigation($module, $context): void {
        Filament::serving(function () use ($module, $context) {
            Filament::forContext('filament', function () use ($module, $context) {
                app(FilamentModules::class)::registerFilamentNavigationItem($module, $context);
            });
            Filament::forContext($context, function () use ($module, $context) {
                app(FilamentModules::class)::renderContextNavigation($module, $context);
            });
        });
    }
    */
    /*
     public function boot()
    {
        parent::boot();
        Filament::serving(function () {
            Filament::forContext('filament', function (){
                Filament::registerNavigationItems([
                    NavigationItem::make(static::$module)->label(static::$module.' Module')->url(route(static::$name.'.pages.dashboard'))->icon('heroicon-o-bookmark')->group('Modules')
                ]);
            });
            Filament::forContext(static::$name, function (){
                Filament::registerRenderHook('sidebar.start',fn():string => \Blade::render('<div class="p-2 px-6 bg-primary-100 font-black w-full">'.static::$module.' Module</div>'));
            });
        });
    }
    */
}
