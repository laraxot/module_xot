<?php

namespace Modules\Xot\Http\Livewire;

//use Illuminate\Support\Carbon;
use Livewire\Component;

class Test extends Component {
    public $animal = '';
    public $options;

    public function mount() {
        $this->options = ['one' => true, 'two' => false, 'three' => false];
    }

    public function render() {
        $view_params = [];

        return view('xot::livewire.test', $view_params);
    }
}
