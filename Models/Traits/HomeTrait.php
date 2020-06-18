<?php

namespace Modules\Xot\Models\Traits;

//use Laravel\Scout\Searchable;

//----- models------
use Modules\Xot\Models\Widget;

//---- services -----
//use Modules\Xot\Services\PanelService as Panel;

//------ traits ---

trait HomeTrait {
    public function widgets() {
        return $this->morphMany(Widget::class, 'post')
            ->where('layout_position', '')
            ->orderBy('pos');
    }
}
