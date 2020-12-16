<?php

declare(strict_types=1);

namespace Modules\Xot\Views\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component as IlluminateComponent;

abstract class XotBaseComponent extends IlluminateComponent {
    /** @var array */
    protected static $assets = [];
    public $attrs = [];

    public static function assets(): array {
        return static::$assets;
    }

    public function getView() {
        $class = get_class($this);

        $module_name = Str::between($class, 'Modules\\', '\Views\\');
        $module_name_low = Str::lower($module_name);

        $comp_name = Str::after($class, '\Views\Components\\');
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

    public function render(): View { //per fare copia ed incolla
        $view = $this->getView();
        $view_params = [
            'view' => $view,
        ];

        return view($view, $view_params);
    }
}
