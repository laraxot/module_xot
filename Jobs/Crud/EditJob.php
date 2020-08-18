<?php

namespace Modules\Xot\Jobs\Crud;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
//----------- Requests ----------
//------------ services ----------
use Illuminate\Support\Str;
use Modules\Xot\Services\PanelService as Panel;

class EditJob implements ShouldQueue {
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
    public function __construct($containers, $items, $data = null) {
        $container = last($containers);
        $item = last($items);
        $this->container = $container;
        $this->item = $item;
        $this->row = $item;
        //$this->panel = Panel::get($this->row);
        $panel_parent = null;
        foreach ($items as $k => $v) {
            $panel = Panel::get($v);
            $panel->setParent($panel_parent);
            $panel_parent = $panel;
        }

        $types = Str::camel(Str::plural($container));
        $rows = null;
        if (is_object($item)) {
            try {
                $rows = $item->$types();
            } catch (\Exception $e) {
            }
        }
        $panel->setRows($rows);
        $this->panel = $panel;
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
