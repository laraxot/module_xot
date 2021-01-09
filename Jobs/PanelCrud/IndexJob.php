<?php

namespace Modules\Xot\Jobs\PanelCrud;

//----------- Requests ----------
//------------ services ----------

/**
 * Class IndexJob
 * @package Modules\Xot\Jobs\PanelCrud
 */
class IndexJob extends XotBaseJob {
    /**
     * @return \Modules\Xot\Contracts\PanelContract
     */
    public function handle() {
        return $this->panel;
    }
}
