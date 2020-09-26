<?php

namespace Modules\Xot\Models\Panels\Actions;

//-------- models -----------

//-------- services --------
use Modules\Xot\Models\Panels\Actions\XotBasePanelAction;

//-------- bases -----------

class TestAction extends XotBasePanelAction {
    public $onItem = true;
    public $onContainer = true;

    public function handle() {
        return $this->panel->view();
    }
}
