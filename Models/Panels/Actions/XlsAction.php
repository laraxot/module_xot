<?php

declare(strict_types=1);

namespace Modules\Xot\Models\Panels\Actions;

// -------- models -----------

// -------- services --------
use Modules\Xot\Services\ArrayService;

// -------- bases -----------

/**
 * Class XlsAction.
 */
class XlsAction extends XotBasePanelAction
{
    public bool $onContainer = true; // onlyContainer

    public string $icon = '<i class="far fa-file-excel fa-1x"></i>';

    /**
     * return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|string|\Symfony\Component\HttpFoundation\BinaryFileResponse.
     *
     * @return \Illuminate\Contracts\Support\Renderable|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function handle()
    {
        $data = $this->rows->get()->toArray();

        $filename = 'test';

        return ArrayService::make()
            ->setArray($data)
            ->setFilename($filename)
            ->toXls();
    }
}
