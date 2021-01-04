<?php

namespace Modules\Xot\Http\Livewire;

use Livewire\Component;
use Modules\Blog\Models\Favorite as FavoriteModel;
use Modules\Xot\Services\PanelService;

class RateIt extends Component {
    //public $model;
    public $post_id;
    public $post_type;
    public $auth_user_id;
    //--------------
    public $modal_guid;
    public $modal_title;

    //public $fav;

    public function mount($model) {
        $this->post_type = PanelService::get($model)->postType();
        $this->post_id = $model->getKey();
        $this->auth_user_id = \Auth::id();
        $this->modal_guid = 'modalrateit';
        $this->modal_title = 'Vota';
        /*
        $fav = FavoriteModel::where('auth_user_id', $this->auth_user_id)
            ->where('post_type', $this->post_type)
            ->where('post_id', $this->post_id)
            ->first();

        $this->fav = false;
        if (is_object($fav)) {
            $this->fav = true;
        }
        */
    }

    public function render() {
        $view = 'xot::livewire.rate_it';
        $view_params = [
            'view' => $view,
            'time' => rand(1, 1000),
        ];

        return view($view, $view_params);
    }
}
