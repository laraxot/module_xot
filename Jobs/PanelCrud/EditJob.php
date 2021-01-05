<?php

namespace Modules\Xot\Jobs\PanelCrud;

//----------- Requests ----------
//------------ services ----------

class EditJob extends XotBaseJob {
    public function handle() {
        return $this->panel;
    }
}
