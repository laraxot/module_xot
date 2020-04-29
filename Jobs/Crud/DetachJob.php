<?php

namespace Modules\Xot\Jobs\Crud;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
//----------- Requests ----------
//------------ services ----------
use Modules\Xot\Services\PanelService as Panel;

class DetachJob implements ShouldQueue
{
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
    public function __construct($container, $item, $data = null)
    {
        $this->container = $container;
        $this->item = $item;
        $this->row = $item;
        $this->panel = Panel::get($this->row);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //$this->row->detach();
        [$containers,$items]=params2ContainerItem();
        \Session::flash('status', 'scolletato');
        $items[0]->bellBoys()->detach($this->row->id);
        return $this->panel;
    }
}
