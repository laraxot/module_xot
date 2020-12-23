<?php

namespace Modules\Xot\Http\Livewire;

use Livewire\Component;
use Modules\Blog\Models\Favorite as FavoriteModel;
use Modules\Xot\Services\PanelService;

class Favorite extends Component {
    //public $model;
    public $post_id;
    public $post_type;
    public $auth_user_id;
    public $fav;

    public function mount($model) {
        $this->post_type = PanelService::get($model)->postType();
        $this->post_id = $model->getKey();
        $this->auth_user_id = \Auth::id();

        $fav = FavoriteModel::where('auth_user_id', $this->auth_user_id)
            ->where('post_type', $this->post_type)
            ->where('post_id', $this->post_id)
            ->first();

        $this->fav = false;
        if (is_object($fav)) {
            $this->fav = true;
        }
    }

    public function render() {
        $view = 'xot::livewire.favorite';
        $view_params = [
            'view' => $view,
        ];

        return view($view, $view_params);
    }

    public function update() {
        $data = [
            'auth_user_id' => $this->auth_user_id,
            'post_type' => $this->post_type,
            'post_id' => $this->post_id,
        ];

        if ($this->fav) {
            FavoriteModel::where($data)->delete();
        } else {
            FavoriteModel::firstOrCreate($data);
        }
        $this->fav = ! $this->fav;
    }
}
/*
window.livewire.on('alert', param => {
        toastr[param['type']](param['message']);
    });

$this->emit('alert', ['type' => 'success', 'message' => 'Agent has been changed.']);
*/
