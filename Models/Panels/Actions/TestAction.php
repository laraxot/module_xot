<?php

namespace Modules\Xot\Models\Panels\Actions;

//-------- models -----------

//-------- services --------
use Modules\Xot\Models\Panels\Actions\XotBasePanelAction;

//-------- bases -----------

/**
 * Class TestAction
 * @package Modules\Xot\Models\Panels\Actions
 */
class TestAction extends XotBasePanelAction {
    /**
     * @var bool
     */
    public bool $onItem = true;
    /**
     * @var bool
     */
    public bool $onContainer = true;

    /**
     * @return mixed
     */
    public function handle() {
        return $this->panel->view();
    }
}
