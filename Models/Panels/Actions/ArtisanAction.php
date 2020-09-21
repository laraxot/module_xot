<?php

namespace Modules\Xot\Models\Panels\Actions;

//-------- models -----------

//-------- services --------
use Modules\Xot\Models\Panels\Actions\XotBasePanelAction;
//-------- bases -----------
use Modules\Xot\Services\ArtisanService;

class ArtisanAction extends XotBasePanelAction {
    public $onContainer = false; //onlyContainer
    public $onItem = true; //onlyContainer
    public $icon = '<i class="far fa-file-excel fa-1x"></i>';

    protected $cmd;
    protected $cmd_params;

    public function __construct($cmd, $cmd_params = []) {
        $this->cmd = $cmd;
        $this->cmd_params = $cmd_params;
    }

    public function handle() {
        $out = ArtisanService::act($this->cmd);

        return $out;
    }

    //end handle
}
