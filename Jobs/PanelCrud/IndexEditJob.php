<?php

declare(strict_types=1);

namespace Modules\Xot\Jobs\PanelCrud;

use Exception;
use Modules\Xot\Contracts\PanelContract;

// ----------- Requests ----------
// ------------ services ----------

/**
 * Class IndexEditJob.
 */
class IndexEditJob extends XotBaseJob {
    public function handle(): PanelContract {
        if ('POST' === \Request::getMethod()) {
            //return IndexUpdateJob::dispatchNow($this->data, $this->panel);
            throw new Exception('['.__LINE__.']['.__FILE__.']');
        }

        return $this->panel;
    }
}
