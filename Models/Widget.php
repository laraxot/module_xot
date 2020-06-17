<?php

namespace Modules\Xot\Models;

//------ ext models---

class Widget extends BaseModel {
    protected $fillable = [
        'id', 'title', 'subtitle',
        'blade', 'pos', 'model', 'limit',
        'order_by', 'image_src',
    ];

    public function linked() {
        return $this->morphTo('post');
    }

    public function getPosAttribute($value) {
        if (null != $value) {
            return $value;
        }
        $value = self::max('pos') + 1;

        return $value;
    }

    public function toHtml() {
        $view = 'pub_theme::layouts.widgets.'.$this->blade;
        $view_params = [
            'lang' => \App::getLocale(),
            'view' => $view,
            'row' => $this->linked,
            'widget' => $this,
        ];
        if (! view()->exists($view)) {
            dddx('View ['.$view.'] Not Exists !');
        }

        return view($view)->with($view_params);
    }
}
