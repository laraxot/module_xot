<?php

namespace Modules\Xot\Contracts;

use Illuminate\Support\Collection;

/**
 * Interface PanelPresenterContract
 * @package Modules\Xot\Contracts
 */
interface PanelPresenterContract {
    /**
     * @param Collection|null $items
     * @return mixed
     */
    public function index(?Collection $items);

    /**
     * @param PanelContract $panel
     * @return mixed
     */
    public function setPanel(PanelContract &$panel);
}
