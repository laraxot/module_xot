<?php

namespace Modules\Xot\Jobs\PanelCrud;

//----------- Requests ----------
//------------ services ----------

class IndexStoreAttachJob extends XotBaseJob {
    public function handle() {
        //dddx([$this->panel, $this->data]);
        //$this->panel->rows->attach($this->data['groups']['to']);
        //return 'preso';
        $to = $this->data['groups']['to'];
        if (! is_array($to)) {
            $to = [];
        }
        $related = $this->panel->rows->getRelated();
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
