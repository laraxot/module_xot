<?php

namespace Modules\Xot\Models\Panels\Actions;

//-------- models -----------

//-------- services --------
use Modules\Xot\Models\Panels\Actions\XotBasePanelAction;
//-------- bases -----------
use Modules\Xot\Services\ArtisanService;

/**
 * Class ArtisanAction
 * @package Modules\Xot\Models\Panels\Actions
 */
class ArtisanAction extends XotBasePanelAction {
    /**
     * @var bool
     */
    public $onContainer = false; //onlyContainer
    /**
     * @var bool
     */
    public $onItem = true; //onlyContainer
    /**
     * @var string
     */
    public $icon = '<i class="far fa-file-excel fa-1x"></i>';

    /**
     * @var
     */
    protected $cmd;
    /**
     * @var array
     */
    protected $cmd_params;

    /**
     * ArtisanAction constructor.
     * @param $cmd
     * @param array $cmd_params
     */
    public function __construct($cmd, $cmd_params = []) {
        $this->cmd = $cmd;
        $this->cmd_params = $cmd_params;
    }

    /**
     * @return string
     */
    public function handle() {
        $out = ArtisanService::act($this->cmd);

        return $out;
    }

    //end handle
}
