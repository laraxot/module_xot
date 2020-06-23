<?php

namespace Modules\Xot\Models\Traits;

//use Laravel\Scout\Searchable;

//----- models------
use Modules\Xot\Models\Widget;

//---- services -----
//use Modules\Xot\Services\PanelService as Panel;

//------ traits ---

trait WidgetTrait {
    public function widgets() {
        return $this->morphMany(Widget::class, 'post')
            ->where('layout_position', '')
            ->orderBy('pos');
    }

    public function widgetsPostType() {
        return $this->hasMany(Widget::class, 'post_type', 'post_type');
        //->whereNull('post_id');
    }
}
