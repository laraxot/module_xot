<?php

namespace Modules\Xot\Transformers\Panels;

use Illuminate\Http\Request;
//--- Services --

use Modules\Xot\Models\Panels\XotBasePanel;

class GeoJsonResourcePanel extends XotBasePanel {
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = 'Modules\Xot\Transformers\GeoJsonResource';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static string $title = 'title';


    

    /**
     * Get the fields displayed by the resource.
     *  'col_bs_size' => 6,
     *   'sortable' => 1,
     *   'rules' => 'required',
     *   'rules_messages' => ['it'=>['required'=>'Nome Obbligatorio']],
     *   'value'=>'..',
     *
     * @return array
     */
    public function fields(): array {
        return [];
    }

    /**
     * Get the tabs available.
     *
     * @return array<array-key, mixed>
     */
    public function tabs():array {
        $tabs_name = [];

        return $tabs_name;
    }

    /**
     * Get the cards available for the request.
     *
     * @return array
     */
    public function cards(Request $request) {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array<array-key, mixed>
     */
    public function filters(Request $request = null):array {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @return array
     */
    public function lenses(Request $request) {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array<array-key, mixed>
     */
    public function actions():array {
        return [];
    }
}