<?php

declare(strict_types=1);

namespace Modules\Xot\Contracts;

use Illuminate\Support\Collection;

/**
 * Interface PanelPresenterContract.
 */
interface PanelPresenterContract {
    /**
     * @return mixed
     */
    public function index(?Collection $items);

    /**
     * @return mixed
     */
    public function setPanel(PanelContract &$panel);

    public function out(?array $params = null);
}
