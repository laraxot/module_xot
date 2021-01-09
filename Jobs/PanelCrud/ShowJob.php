<?php

namespace Modules\Xot\Jobs\PanelCrud;

//----------- Requests ----------
//------------ services ----------

/**
 * Class ShowJob
 * @package Modules\Xot\Jobs\PanelCrud
 */
class ShowJob extends XotBaseJob {
    /**
     * @return \Modules\Xot\Contracts\PanelContract
     */
    public function handle() {
        return $this->panel;
    }
}
