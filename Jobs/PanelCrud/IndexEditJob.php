<?php

namespace Modules\Xot\Jobs\PanelCrud;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

//----------- Requests ----------
//------------ services ----------

class IndexEditJob implements ShouldQueue {
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Traits\CommonTrait;

    protected $container;
    protected $item;
    protected $row;
    protected $rows;
    protected $data;

    protected $panel;

    public function __construct($request, $panel) {
        $this->panel = $panel;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        if ('POST' == \Request::getMethod()) {
            return IndexUpdateJob::dispatchNow($this->container, $this->item);
        }

        return $this->panel;
    }
}
