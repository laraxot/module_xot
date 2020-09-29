<?php

namespace Modules\Xot\Jobs\PanelCrud;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

//----------- Requests ----------
//------------ services ----------

class IndexAttachJob implements ShouldQueue {
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    //use Traits\CommonTrait;

    protected $panel;

    public function __construct($request, $panel) {
        $this->request = $request;
        $this->panel = $panel;
    }

    public function handle() {
        if ('POST' == \Request::getMethod()) {
            $this->panel = IndexStoreAttachJob::dispatchNow($this->request, $this->panel);
        }

        return $this->panel;
    }
}
