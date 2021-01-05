<?php

namespace Modules\Xot\Jobs\PanelCrud;

//----------- Requests ----------
//------------ services ----------

class ShowJob extends XotBaseJob {
    public function handle() {
        return $this->panel;
    }
}
