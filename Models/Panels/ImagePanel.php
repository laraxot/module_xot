<?php

namespace Modules\Xot\Models\Panels;

use Illuminate\Http\Request;

//--- Services --

/**
 * Class ImagePanel
 * @package Modules\Xot\Models\Panels
 */
class ImagePanel extends XotBasePanel {
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    protected static string $model = 'Modules\Xot\Models\Image';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    protected static string $title = 'title';

    /**
     * Get the actions available for the resource.
     *
     * @param Request|null $request
     * @return array
     */
    public function actions(Request $request = null) {
        return [
            new Actions\ChunkUpload(),
        ];
    }
}
