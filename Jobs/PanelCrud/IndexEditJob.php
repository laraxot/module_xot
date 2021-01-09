<?php

namespace Modules\Xot\Jobs\PanelCrud;

//----------- Requests ----------
//------------ services ----------

/**
 * Class IndexEditJob
 * @package Modules\Xot\Jobs\PanelCrud
 */
class IndexEditJob extends XotBaseJob {
    /**
     * @return mixed|\Modules\Xot\Contracts\PanelContract
     */
    public function handle() {
        if ('POST' == \Request::getMethod()) {
            return IndexUpdateJob::dispatchNow($this->data, $this->panel);
        }

        return $this->panel;
    }
}
