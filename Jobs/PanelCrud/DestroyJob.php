<?php

namespace Modules\Xot\Jobs\PanelCrud;

//----------- Requests ----------
//------------ services ----------

class DestroyJob extends XotBaseJob {
    public function handle() {
        $this->panel->row->delete();
        \Session::flash('status', 'eliminato');

        return $this->panel;
    }
}
