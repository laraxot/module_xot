<?php

namespace Modules\Xot\Http\Livewire;

use Modules\Xot\Services\ColumnService;
use Modules\Xot\Services\PanelService;

//use Illuminate\Support\Carbon;

class Edit extends XotBaseFormComponent {
    public $index_fields = [];
    public $route_params = [];
    public $data = [];

    public function mount() {
        $this->route_params = request()->route()->parameters();
        $this->data = request()->all();
        $this->setTableProperties();
    }

    public function getPanelProperty() {
        return PanelService::getByParams($this->route_params);
    }

    public function query() {
        return $this->panel->rows($this->data);
    }

    public function columns() {
        $columns = [];
        $this->index_fields = $this->panel->indexFields();

        foreach ($this->index_fields as $field) {
            if (is_array($field)) {
                $field = (object) $field;
            }
            $col = ColumnService::make($field->name);
            if (in_array($field->type, ['Id', 'String', 'Text', 'Integer'])) {
                $col->sortable()->searchable();
            }

            $columns[] = $col;
        }

        return $columns;
    }
}
