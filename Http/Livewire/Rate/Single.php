<?php

namespace Modules\Xot\Http\Livewire\Rate;

use Livewire\Component;
use Modules\Blog\Models\RatingMorph;
use Modules\Xot\Services\PanelService;

class Single extends Component {
    public $model;
    public $goal;
    public $val;
    public $post_id;
    public $post_type;
    public $auth_user_id;
    //--------------
    public $modal_guid;
    public $modal_title;

    //public $fav;

    public function mount($model, $goal) {
        $this->model = $model;
        $this->goal = $goal;
        $this->post_type = PanelService::get($model)->postType();
        $this->post_id = $model->getKey();
        $this->auth_user_id = \Auth::id();
        $this->modal_guid = 'modalrateit';
        $this->modal_title = 'Vota';
    }

    public function render() {
        $view = 'xot::livewire.rate.single';
        $val = RatingMorph::where('auth_user_id', $this->auth_user_id)
        ->where('post_type', $this->post_type)
        ->where('post_id', $this->post_id)
        ->where('rating_id', $this->goal->id)
        ->first();

        $this->val = null;
        if (is_object($val)) {
            $this->val = $val->rating;
        }
        $view_params = [
            'view' => $view,
            'time' => rand(1, 1000),
        ];

        return view($view, $view_params);
    }

    public function update($val) {
        session()->flash('message', 'Users Created Successfully.['.$val.']');
        $data = [
            'auth_user_id' => $this->auth_user_id,
            'post_type' => $this->post_type,
            'post_id' => $this->post_id,
            'rating_id' => $this->goal->id,
        ];
        $morph = RatingMorph::firstOrCreate($data);
        $morph->rating = $val;
        $morph->save();
    }
}
