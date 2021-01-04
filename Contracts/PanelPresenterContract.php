<?php

namespace Modules\Xot\Contracts;

use Illuminate\Support\Collection;

interface PanelPresenterContract {
    public function index(?Collection $items);
}
