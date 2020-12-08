<?php

namespace Modules\Xot\Http\Livewire\Rate;

use Livewire\Component;
use Modules\Blog\Models\Rating;
use Modules\Xot\Services\PanelService;

class Multi extends Component {
    public $model;
    public $post_id;
    public $post_type;
    public $auth_user_id;
    //--------------
    public $modal_guid;
    public $modal_title;

    //public $fav;

    public function mount($model) {
        $this->model = $model;
        $this->post_type = PanelService::get($model)->postType();
        $this->post_id = $model->getKey();
        $this->auth_user_id = \Auth::id();
        $this->modal_guid = 'modalrateit';
        $this->modal_title = 'Vota';
    }

    public function render() {
        $view = 'xot::livewire.rate.multi';
        $goals = Rating::where('related_type', $this->post_type)->get();

        $view_params = [
            'view' => $view,
            'goals' => $goals,
        ];

        return view($view, $view_params);
    }

    public function cancel() {
        //$this->updateMode = false;
        //$this->resetInputFields();
    }

    private function resetInputFields() {
        //$this->name = '';
        //$this->email = '';
    }
}
