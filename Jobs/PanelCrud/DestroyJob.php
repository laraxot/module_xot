<?php

declare(strict_types=1);

namespace Modules\Xot\Jobs\PanelCrud;

use Modules\Xot\Contracts\PanelContract;

//----------- Requests ----------
//------------ services ----------

/**
 * Class DestroyJob.
 */
class DestroyJob extends XotBaseJob {
    public function handle(): PanelContract {
        $this->panel->row->delete();
        \Session::flash('status', 'eliminato');

        return $this->panel;
    }
}
