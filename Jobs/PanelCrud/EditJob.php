<?php

namespace Modules\Xot\Jobs\PanelCrud;

//----------- Requests ----------
//------------ services ----------

/**
 * Class EditJob
 * @package Modules\Xot\Jobs\PanelCrud
 */
class EditJob extends XotBaseJob {
    /**
     * @return \Modules\Xot\Contracts\PanelContract
     */
    public function handle() {
        return $this->panel;
    }
}
