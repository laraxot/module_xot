<?php

namespace Modules\Xot\Jobs\PanelCrud;

//----------- Requests ----------
//------------ services ----------

class IndexAttachJob extends XotBaseJob {
    public function handle() {
        if ('POST' == \Request::getMethod()) {
            $this->panel = IndexStoreAttachJob::dispatchNow($this->data, $this->panel);
        }

        return $this->panel;
    }
}
