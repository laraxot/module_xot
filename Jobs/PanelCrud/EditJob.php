<?php

namespace Modules\Xot\Jobs\PanelCrud;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

//----------- Requests ----------
//------------ services ----------

class EditJob implements ShouldQueue {
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    //use Traits\CommonTrait;

    protected $panel;

    public function __construct($request, $panel) {
        $this->panel = $panel;
    }

    public function handle() {
        return $this->panel;
    }
}
