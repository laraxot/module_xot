<?php

namespace Modules\Xot\Jobs\PanelCrud;

//----------- Requests ----------
//------------ services ----------

/**
 * Class DestroyJob
 * @package Modules\Xot\Jobs\PanelCrud
 */
class DestroyJob extends XotBaseJob {
    /**
     * @return \Modules\Xot\Contracts\PanelContract
     */
    public function handle() {
        $this->panel->row->delete();
        \Session::flash('status', 'eliminato');

        return $this->panel;
    }
}
