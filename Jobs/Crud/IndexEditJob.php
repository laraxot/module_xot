<?php

namespace Modules\Xot\Jobs\Crud;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
//----------- Requests ----------
//------------ services ----------
use Modules\Xot\Services\PanelService as Panel;

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

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($container, $item, $data = null) {
        $this->container = $container;
        $this->item = $item;

        if (! is_object($item)) {
            $row = xotModel($container);
            $rows = $row;
        } else {
            $types = Str::camel(Str::plural($container));
            $rows = $item->$types();
            $row = $rows->getRelated();
        }
        $this->row = $row;
        $this->rows = $rows;
        $this->panel = Panel::get($row);
        $this->panel->setRows($rows);
        //ddd($this->panel);
        /*
    if($data==null){
    //$data=$this->getData();
    }
    $this->data=$data;
     */
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
