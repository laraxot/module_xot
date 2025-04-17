<?php

declare(strict_types=1);

namespace Modules\Xot\Providers;

use Illuminate\Support\Facades\Blade;
use Modules\Xot\Services\BladeService;
use Modules\Xot\Services\LivewireService;

/**
 * Class XotBaseThemeServiceProvider.
 *
 * @property string $dir
 * @property string $name
 */
abstract class XotBaseThemeServiceProvider
{
    public string $dir = '';
    public string $name = '';
    public string $ns = '';

    public function bootCallback(): void
    {
        /*
        $blade_component_path = '\Themes\LaravelIo\View\Components';
        foreach ($this->blade_components as $name => $class) {
            Blade::component($name, $blade_component_path.'\\'.$class);
        }
        */
        $this->registerBladeDirective();
        $this->registerBladeComponents();
        $this->registerLivewireComponents();
    }

    /**
     * Undocumented function.
     */
    public function registerBladeDirective(): void
    {
        Blade::directive(
            'md',
            function ($expression) {
                return '<'."?php echo md_to_html({$expression}); ?".'>';
            }
        );

        Blade::directive(
            'formGroup',
            function ($expression) {
                return '<div class="form-group<'."?php echo \$errors->has({$expression}) ? ' has-error' : '' ?".'>">';
            }
        );

        Blade::directive(
            'endFormGroup',
            function ($expression) {
                return '</div>';
            }
        );

        Blade::directive(
            'title',
            function ($expression) {
                return '<'."?php \$title = {$expression} ?".'>';
            }
        );

        Blade::directive(
            'shareImage',
            function ($expression) {
                return '<'."?php \$shareImage = {$expression} ?".'>';
            }
        );

        Blade::directive(
            'canonical',
            function ($expression) {
                return '<'."?php \$canonical = {$expression} ?".'>';
            }
        );
    }

    public function registerBladeComponents(): void
    {
        BladeService::registerComponents($this->dir.'/../View/Components', 'Themes\\'.$this->name);
    }

    public function registerLivewireComponents(): void
    {
        LivewireService::registerComponents($this->dir.'/../Http/Livewire', 'Themes\\'.$this->name);
    }
}
