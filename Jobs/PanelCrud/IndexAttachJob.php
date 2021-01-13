<?php

declare(strict_types=1);

namespace Modules\Xot\Jobs\PanelCrud;

use Modules\Xot\Contracts\PanelContract;

//----------- Requests ----------
//------------ services ----------

/**
 * Class IndexAttachJob.
 */
class IndexAttachJob extends XotBaseJob {
    public function handle(): PanelContract {
        if ('POST' == \Request::getMethod()) {
            $this->panel = IndexStoreAttachJob::dispatchNow($this->data, $this->panel);
        }

        return $this->panel;
    }
}
