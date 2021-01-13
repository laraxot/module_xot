<?php

declare(strict_types=1);

namespace Modules\Xot\Jobs\PanelCrud;

use Modules\Xot\Contracts\PanelContract;

//----------- Requests ----------
//------------ services ----------

/**
 * Class IndexOrderJob.
 */
class IndexOrderJob extends XotBaseJob {
    public function handle(): PanelContract {
        return $this->panel;
    }
}
