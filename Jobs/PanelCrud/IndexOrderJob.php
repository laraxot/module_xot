<?php

namespace Modules\Xot\Jobs\PanelCrud;

//----------- Requests ----------
//------------ services ----------

/**
 * Class IndexOrderJob
 * @package Modules\Xot\Jobs\PanelCrud
 */
class IndexOrderJob extends XotBaseJob {
    /**
     * @return \Modules\Xot\Contracts\PanelContract
     */
    public function handle() {
        return $this->panel;
    }
}
