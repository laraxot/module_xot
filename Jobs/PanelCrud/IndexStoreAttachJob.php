<?php

namespace Modules\Xot\Jobs\PanelCrud;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

//----------- Requests ----------
//------------ services ----------

class IndexStoreAttachJob implements ShouldQueue
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

    protected $panel;

    public function __construct($request, $panel)
    {
        $this->panel = $panel;
        $this->data = $request->all();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //dddx([$this->panel, $this->data]);
        //$this->panel->rows->attach($this->data['groups']['to']);
        //return 'preso';
        $to=$this->data['groups']['to'];
        if (!is_array($to)) {
            $to=[];
        }
        $related=$this->panel->rows->getRelated();
        $items_key = $related->getKeyName();

        $items_0 = $this->panel->rows->get()->pluck($items_key);

        
        $items_1 = collect($to);
        $items_add = $items_1->diff($items_0);
        $items_sub = $items_0->diff($items_1);
        //$items->detach($items_sub->all());

        //dddx(['add'=>$items_add,'sub'=>$items_sub]);
        $this->panel->rows->detach($items_sub->all());
        $this->panel->rows->attach($items_add->all());

        $status = 'collegati ['.\implode(', ', $items_add->all()).'] scollegati ['.\implode(', ', $items_sub->all()).']';
        \Session::flash('status', $status);






        return $this->panel;
    }
}
