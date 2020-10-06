<?php

namespace Modules\Xot\Models\Panels\Actions;

//-------- models -----------

//-------- services --------
use Modules\Xot\Services\ArrayService;

//-------- bases -----------

class CloneAction extends XotBasePanelAction
{
    public $onItem = true;
    public $icon = '<i class="far fa-clone"></i>';

    public function handle()
    {
        $cloned=$this->row->replicate();
        $cloned->push();
        return redirect()->back();
    }
}
