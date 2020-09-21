<?php

namespace Modules\Xot\Models\Traits;

//use Laravel\Scout\Searchable;

//----- models------
use Modules\Xot\Models\Widget;

//---- services -----
//use Modules\Xot\Services\PanelService as Panel;

//------ traits ---

trait WidgetTrait {
    public function widgets() {  //questo sarebbe itemWidgets, ma teniamo questo nome
        return $this->morphMany(Widget::class, 'post')
            //->whereNull('layout_position')
            ->where(function ($query) {
                $query->where('layout_position', '')
                    ->orWhereNull('layout_position');
            })
            ->orderBy('pos');
    }

    public function containerWidgets() {
        return $this->hasMany(Widget::class, 'post_type', 'post_type')
            ->orderBy('pos');
        //->whereNull('post_id');
    }

    //non sembra funzionare, perchè?
    public function scopeOfLayoutPosition($query, $layout_position) {
        return $query->where('layout_position', $layout_position);
    }
}
