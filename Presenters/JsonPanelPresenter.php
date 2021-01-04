<?php

namespace Modules\Xot\Presenters;

use Illuminate\Support\Collection;
use Modules\Xot\Contracts\PanelPresenterContract;
use Modules\Xot\Services\StubService;

class JsonPanelPresenter implements PanelPresenterContract {
    protected $panel;

    public function setPanel($panel) {
        $this->panel = $panel;
    }

    public function index(?Collection $items) {
    }

    public function outContainer($params = null) {
        $model = $this->panel->row;
        $transformer = StubService::fromModel(['model' => $model, 'stub' => 'transformer_collection']);
        $rows = $this->panel->rows()->paginate(20);
        $out = new $transformer($rows);

        return $out;
    }

    public function outItem($params = null) {
        $model = $this->panel->row;
        $transformer = StubService::fromModel(['model' => $model, 'stub' => 'transformer_resource']);
        $out = new $transformer($model);

        return $out;
    }

    public function out($params = null) {
        if (isContainer()) {
            return $this->outContainer($params);
        }

        return $this->outItem($params);
    }
}
