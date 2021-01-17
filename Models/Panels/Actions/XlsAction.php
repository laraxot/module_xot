<?php

namespace Modules\Xot\Models\Panels\Actions;

//-------- models -----------

//-------- services --------
use Modules\Xot\Services\ArrayService;

//-------- bases -----------

/**
 * Class XlsAction
 * @package Modules\Xot\Models\Panels\Actions
 */
class XlsAction extends XotBasePanelAction {
    /**
     * @var string
     */
    public ?string $name = 'xls'; //name for calling Action
    /**
     * @var bool
     */
    public bool $onContainer = true; //onlyContainer
    /**
     * @var string
     */
    public string $icon = '<i class="far fa-file-excel fa-1x"></i>';

    /**
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|string|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function handle() {
        $data = ($this->rows->get()->toArray());

        return ArrayService::toXls(['data' => $data, 'filename' => 'test']);
    }
}
