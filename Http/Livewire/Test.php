<?php

namespace Modules\Xot\Http\Livewire;

//use Illuminate\Support\Carbon;
use Livewire\Component;

/**
 * Class Test
 * @package Modules\Xot\Http\Livewire
 */
class Test extends Component {
    /**
     * @var string
     */
    public $animal = '';
    /**
     * @var
     */
    public $options;
    /**
     * @var array
     */
    public $products = [];
    /**
     * @var array
     */
    public $change_cats = [];
    /**
     * @var array
     */
    public $changes = [];
    /**
     * @var array
     */
    public $qty = [];
    /**
     * @var array
     */
    public $qty1 = [];

    public function mount() {
        $this->options = ['one' => true, 'two' => false, 'three' => false];
        //$this->qty = [0 => -1, 1 => 1, 2 => 0, 3 => 0, 4 => -1];
        $this->products = [
            (object) ['id' => 1, 'title' => 'Margherita'],
            (object) ['id' => 2, 'title' => 'Capricciosa'],
        ];
        $this->change_cats = [
            (object) ['id' => 1, 'title' => 'Formaggi'],
            (object) ['id' => 2, 'title' => 'Salumi'],
        ];
        $this->changes = [
            (object) ['id' => 1, 'id_cat' => 1, 'title' => 'mozzarella'],
            (object) ['id' => 2, 'id_cat' => 1, 'title' => 'gorgonzola'],
            (object) ['id' => 3, 'id_cat' => 2, 'title' => 'salame'],
            (object) ['id' => 4, 'id_cat' => 2, 'title' => 'prosciutto'],
        ];
    }

    /**
     * @param $arr
     * @return \Illuminate\Support\Collection
     */
    public function fix($arr) {
        return collect($arr)->map(
            function ($item) {
                return (object) $item;
            }
        ); //->all();
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render() {
        $view_params = [];
        $this->products = $this->fix($this->products);
        $this->change_cats = $this->fix($this->change_cats);
        $this->changes = $this->fix($this->changes);

        return view('xot::livewire.test', $view_params);
    }
}
