<?php

namespace Modules\Xot\Jobs\PanelCrud;

//----------- Requests ----------
//------------ services ----------

class IndexEditJob extends XotBaseJob {
    public function handle() {
        if ('POST' == \Request::getMethod()) {
            return IndexUpdateJob::dispatchNow($this->data, $this->panel);
        }

        return $this->panel;
    }
}
