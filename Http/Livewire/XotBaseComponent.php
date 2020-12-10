<?php

namespace Modules\Xot\Http\Livewire;

//use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Component;

abstract class XotBaseComponent extends Component {
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

    public function render() { //per fare copia ed incolla
        $view = $this->getView();
        $view_params = [
            'view' => $view,
        ];

        return view($view, $view_params);
    }
}
