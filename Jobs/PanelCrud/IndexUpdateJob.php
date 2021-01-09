<?php

namespace Modules\Xot\Jobs\PanelCrud;

//----------- Requests ----------
//------------ services ----------

/**
 * Class IndexUpdateJob
 * @package Modules\Xot\Jobs\PanelCrud
 */
class IndexUpdateJob extends XotBaseJob {
    /**
     * @return \Modules\Xot\Contracts\PanelContract
     */
    public function handle() {
        dddx('WIP');

        return $this->panel;
    }
}
