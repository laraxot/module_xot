<?php

namespace Modules\Xot\Http\Livewire;

//use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Component;

/**
 * Class XotBaseComponent.
 */
abstract class XotBaseComponent extends Component {
    /**
     * @return string
     */
    public function getView() {
        $class = get_class($this);
        $module_name = Str::between($class, 'Modules\\', '\Http\\');
        $module_name_low = Str::lower($module_name);
        $comp_name = Str::after($class, '\Http\Livewire\\');
        $comp_name = str_replace('\\', '.', $comp_name);
        $comp_name = Str::snake($comp_name);
        $view = $module_name_low.'::livewire.'.$comp_name;
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

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render() { //per fare copia ed incolla
        $view = $this->getView();
        $view_params = [
            'view' => $view,
        ];

        return view()->make($view, $view_params);
    }
}