<?php

namespace Modules\Xot\Models\Panels\Actions;

//-------- models -----------

//-------- services --------

//-------- bases -----------

class ArtisanAction extends XotBasePanelAction {
    public $name = 'xls'; //name for calling Action
    public $onContainer = false; //onlyContainer
    public $onItem = true; //onlyContainer
    public $icon = '<i class="far fa-file-excel fa-1x"></i>';

    public function handle() {
        return 'ciao';
    }
}
