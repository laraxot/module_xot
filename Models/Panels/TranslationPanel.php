<?php

declare(strict_types=1);

namespace Modules\Xot\Models\Panels;

//--- Services --

/**
 * Class TranslationPanel.
 */
class TranslationPanel extends XotBasePanel {
    /**
     * The model the resource corresponds to.
     */
    protected static string $model = 'Modules\Xot\Models\Translation';

    /**
     * Undocumented function.
     */
    public function actions(): array {
        return [
            new Actions\ClearDuplicatesTransAction(),
        ];
    }
}
