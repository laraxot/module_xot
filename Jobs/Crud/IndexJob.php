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

class IndexJob implements ShouldQueue {
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Traits\CommonTrait;

    protected $container;
    protected $item;
    protected $rows;
    protected $row;
    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($containers, $items, $data = null) {
        if (is_array($containers)) {
            $container = last($containers);
        } else {
            $container = $containers;
            $containers = [$container];
        }
        if (is_array($items)) {
            $item = last($items);
        } else {
            $item = $items;
            $items = [$item];
        }
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
        $panel = null;
        $panel_parent = null;
        foreach ($items as $k => $v) {
            $panel = Panel::get($v);
            $panel->setParent($panel_parent);
            $panel_parent = $panel;
        }
        $this->panel = Panel::get($row);
        if (null != $panel) {
            $this->panel->setParent($panel);
        }

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
        return $this->panel;
    }
}
