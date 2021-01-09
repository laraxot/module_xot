<?php

namespace Modules\Xot\Jobs\PanelCrud;

//----------- Requests ----------
//------------ services ----------

/**
 * Class AttachJob
 * @package Modules\Xot\Jobs\PanelCrud
 */
class AttachJob extends XotBaseJob {
    /**
     * @return \Modules\Xot\Contracts\PanelContract
     */
    public function handle() {
        return $this->panel;
    }
}
