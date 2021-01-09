<?php

namespace Modules\Xot\Models\Traits;

//use Laravel\Scout\Searchable;

//----- models------
use Modules\Xot\Models\Widget;

//---- services -----
//use Modules\Xot\Services\PanelService as Panel;

//------ traits ---

/**
 * Trait WidgetTrait
 * @package Modules\Xot\Models\Traits
 */
trait WidgetTrait {
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function widgets() {  //questo sarebbe itemWidgets, ma teniamo questo nome
        return $this->morphMany(Widget::class, 'post')
            //->whereNull('layout_position')
            ->where(function ($query) {
                $query->where('layout_position', '')
                    ->orWhereNull('layout_position');
            })
            ->orderBy('pos');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function containerWidgets() {
        return $this->hasMany(Widget::class, 'post_type', 'post_type')
            ->orderBy('pos');
        //->whereNull('post_id');
    }

    //non sembra funzionare, perchè?

    /**
     * @param $query
     * @param $layout_position
     * @return mixed
     */
    public function scopeOfLayoutPosition($query, $layout_position) {
        return $query->where('layout_position', $layout_position);
    }
}
