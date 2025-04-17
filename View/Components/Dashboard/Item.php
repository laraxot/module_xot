<?php

declare(strict_types=1);

namespace Modules\Xot\View\Components\Dashboard;

use Illuminate\View\Component;

// use Modules\Xot\View\Components\XotBaseComponent;

/**
 * Class Field.
 */
class Item extends Component
{
    public function render(): \Illuminate\Contracts\Support\Renderable
    {
        /**
         * @phpstan-var view-string
         */
        $view = 'xot::components.dashboard.item';
        $view_params = [
            'view' => $view,
        ];

        return view($view, $view_params);
    }
}
