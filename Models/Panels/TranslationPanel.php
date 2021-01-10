<?php

namespace Modules\Xot\Models\Panels;

//--- Services --

/**
 * Class TranslationPanel
 * @package Modules\Xot\Models\Panels
 */
class TranslationPanel extends XotBasePanel {
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    protected static string $model = 'Modules\Xot\Models\Translation';

    /**
     * Undocumented function.
     * @return array

     */
    public function actions(): array {
        return [
            new Actions\ClearDuplicatesTransAction(),
        ];
    }
}
