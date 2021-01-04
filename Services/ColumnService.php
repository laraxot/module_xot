<?php

namespace Modules\Xot\Services;

/*
*  https://github.com/kdion4891/laravel-livewire-tables/blob/master/src/Column.php
*
**/

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @property string   $heading
 * @property string   $attribute
 * @property bool     $searchable
 * @property bool     $sortable
 * @property callable $sortCallback
 * @property string   $view
 */
class ColumnService {
    protected $heading;
    protected $attribute;
    protected $type;
    protected $searchable = false;
    protected $sortable = false;
    protected $sortCallback;
    protected $view;

    public function __construct($heading, $attribute) {
        $this->heading = $heading;
        $this->attribute = $attribute ?? Str::snake(Str::lower($heading));
    }

    public function __get($property) {
        return $this->$property;
    }

    public static function make($heading = null, $attribute = null) {
        return new static($heading, $attribute);
    }

    public function type($type) {
        $this->type = $type;

        return $this;
    }

    public function searchable() {
        $this->searchable = true;

        return $this;
    }

    public function sortable() {
        $this->sortable = true;

        return $this;
    }

    public function sortUsing(callable $callback) {
        $this->sortCallback = $callback;

        return $this;
    }

    public function view($view) {
        $this->view = $view;

        return $this;
    }

    public function freeze($model) {
        $value = Arr::get($model->toArray(), $this->attribute);
        $view = 'formx::livewire.fields.'.Str::snake($this->type).'.freeze';
        $view_params = [
            'view' => $view,
            'row' => $model,
            'value' => $value,
            //'field' => $this,
        ];

        return view($view, $view_params);

        return $value;
    }
}
