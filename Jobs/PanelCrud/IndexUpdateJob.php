<?php

declare(strict_types=1);

namespace Modules\Xot\Jobs\PanelCrud;

use Modules\Xot\Contracts\PanelContract;

//----------- Requests ----------
//------------ services ----------

/**
 * Class IndexUpdateJob.
 */
class IndexUpdateJob extends XotBaseJob {
    public function handle(): PanelContract {
        dddx('WIP');

        return $this->panel;
    }
}
