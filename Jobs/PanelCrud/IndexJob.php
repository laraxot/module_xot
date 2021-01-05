<?php

namespace Modules\Xot\Jobs\PanelCrud;

//----------- Requests ----------
//------------ services ----------

class IndexJob extends XotBaseJob {
    public function handle() {
        return $this->panel;
    }
}
