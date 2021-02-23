<<<<<<< HEAD
<?php

declare(strict_types=1);

namespace Modules\Xot\View\Components;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component as IlluminateComponent;

/**
 * Class XotBaseComponent.
 */
abstract class XotBaseComponent extends IlluminateComponent {
    /** @var array */
    protected static array $assets = [];

    public array $attrs = [];

    public static function assets(): array {
        return static::$assets;
    }

    public function getView(): string {
        $class = get_class($this);

        $module_name = Str::between($class, 'Modules\\', '\Views\\');
        $module_name_low = Str::lower($module_name);

        $comp_name = Str::after($class, '\View\Components\\');
        $comp_name = str_replace('\\', '.', $comp_name);
        $comp_name = Str::snake($comp_name);
        $view = $module_name_low.'::components.'.$comp_name;
        $view = str_replace('._', '.', $view);

        //fare distinzione fra inAdmin o no ?
        if (! view()->exists($view)) {
            dddx([
                'err' => 'View not Exists',
                'view' => $view,
            ]);
        }

        return $view;
    }

    // ret \Closure|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Support\Htmlable|\Illuminate\Contracts\View\Factory|View|string

    public function render(): Renderable { //per fare copia ed incolla
        $view = $this->getView();
        $view_params = [
            'view' => $view,
        ];

        return view()->make($view, $view_params);
    }
}
=======
<?php

declare(strict_types=1);

namespace Modules\Xot\View\Components;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component as IlluminateComponent;

/**
 * Class XotBaseComponent.
 */
abstract class XotBaseComponent extends IlluminateComponent {
    /** @var array */
    protected static array $assets = [];

    public array $attrs = [];

    public static function assets(): array {
        return static::$assets;
    }

    public function getView(): string {
        $class = get_class($this);

        $module_name = Str::between($class, 'Modules\\', '\Views\\');
        $module_name_low = Str::lower($module_name);

        $comp_name = Str::after($class, '\View\Components\\');
        $comp_name = str_replace('\\', '.', $comp_name);
        $comp_name = Str::snake($comp_name);
        $view = $module_name_low.'::components.'.$comp_name;
        $view = str_replace('._', '.', $view);

        //fare distinzione fra inAdmin o no ?
        if (! view()->exists($view)) {
            dddx([
                'err' => 'View not Exists',
                'view' => $view,
            ]);
        }

        return $view;
    }

    // ret \Closure|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Support\Htmlable|\Illuminate\Contracts\View\Factory|View|string

    public function render(): Renderable { //per fare copia ed incolla
        $view = $this->getView();
        $view_params = [
            'view' => $view,
        ];

        return view()->make($view, $view_params);
    }
}
>>>>>>> 3c97c308c85924a62f31c89c71edfe23450749f0
