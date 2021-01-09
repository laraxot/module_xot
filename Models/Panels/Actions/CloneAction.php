<?php

namespace Modules\Xot\Models\Panels\Actions;

//-------- models -----------

//-------- services --------
use Modules\Xot\Services\ArrayService;

//-------- bases -----------

/**
 * Class CloneAction
 * @package Modules\Xot\Models\Panels\Actions
 */
class CloneAction extends XotBasePanelAction
{
    /**
     * @var bool
     */
    public bool $onItem = true;
    /**
     * @var string
     */
    public string $icon = '<i class="far fa-clone"></i>';

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle()
    {
        $cloned=$this->row->replicate();
        $cloned->push();
        return redirect()->back();
    }
}
