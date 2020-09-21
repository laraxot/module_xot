<?php

namespace Modules\Xot\Models\Panels\Actions;

//-------- models -----------

//-------- services --------
use Modules\Xot\Services\ArrayService;

//-------- bases -----------

class XlsAction extends XotBasePanelAction {
    public $name = 'xls'; //name for calling Action
    public $onContainer = true; //onlyContainer
    public $icon = '<i class="far fa-file-excel fa-1x"></i>';

    public function handle() {
        $data = ($this->rows->get()->toArray());

        return ArrayService::toXls(['data' => $data, 'filename' => 'test']);
    }
}
