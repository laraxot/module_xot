<?php

namespace Modules\Xot\Presenters;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Modules\Theme\Services\ThemeService;
use Modules\Xot\Contracts\PanelPresenterContract;

class JsonPanelPresenter implements PanelPresenterContract {
    protected $panel;

    public function setPanel($panel) {
        $this->panel = $panel;
    }


    public function index(?Collection $items) {
    }

    public function out($params = null) {
        return 'preso';
    }
}
