<?php

namespace Modules\Xot\Jobs\PanelCrud;

//----------- Requests ----------
//------------ services ----------

/**
 * Class IndexAttachJob
 * @package Modules\Xot\Jobs\PanelCrud
 */
class IndexAttachJob extends XotBaseJob {
    /**
     * @return mixed|\Modules\Xot\Contracts\PanelContract
     */
    public function handle() {
        if ('POST' == \Request::getMethod()) {
            $this->panel = IndexStoreAttachJob::dispatchNow($this->data, $this->panel);
        }

        return $this->panel;
    }
}
