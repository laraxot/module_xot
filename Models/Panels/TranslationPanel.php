<?php

namespace Modules\Xot\Models\Panels;

//--- Services --

class TranslationPanel extends XotBasePanel {
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    protected static $model = 'Modules\Xot\Models\Translation';

    /**
     * Undocumented function.
     */
    public function actions(): array {
        return [
            new Actions\ClearDuplicatesTransAction(),
        ];
    }
}
