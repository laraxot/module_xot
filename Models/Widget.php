<?php

namespace Modules\Xot\Models;

//------ ext models---

class Widget extends BaseModel {
    protected $fillable = [
        'id', 'post_type', 'title', 'subtitle',
        'blade', 'pos', 'model', 'limit',
        'order_by', 'image_src', 'layout_position',
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

    public function toHtml($params = null) {
        $view = 'pub_theme::layouts.widgets';
        if (null != $this->layout_position) {
            $view .= '.'.$this->layout_position;
        }
        $view .= '.'.$this->blade;
        $view_params = [
            'lang' => \App::getLocale(),
            'view' => $view,
            'row' => $this->linked,
            'widget' => $this,
        ];
        if (null != $params) {
            $view_params['params'] = $params;
        }
        if (! view()->exists($view)) {
            dddx('View ['.$view.'] Not Exists !');
        }
        try{
            return view($view)->with($view_params);
        }catch(\Exception $e){
            dddx($e);
        }
    }
}
