<?php

namespace Modules\Xot\Models\Panels;

use Illuminate\Http\Request;

//--- Services --

/**
 * Class HomePanel
 * @package Modules\Xot\Models\Panels
 */
class HomePanel extends XotBasePanel {
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = 'Modules\Xot\Models\Home';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static string $title = 'title';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static array $search = [];

    /**
     * The relationships that should be eager loaded on index queries.
     */
    public function with():array {
        return ['widgets'];
    }

    /**
     * @return array
     */
    public function search() :array {

        return [];
    }

    /**
     * on select the option id.
     * @param object $row
     * @return mixed
     */
    public function optionId(object $row) {
        return $row->area_id;
    }

    /**
     * on select the option label.
     * @param object $row
     * @return string
     */
    public function optionLabel(object $row):string {
        return $row->area_define_name;
    }

    /**
     * Get the fields displayed by the resource.
     *

     *
     * @return array
        'col_bs_size' => 6,
        'sortable' => 1,
        'rules' => 'required',
        'rules_messages' => ['it'=>['required'=>'Nome Obbligatorio']],
        'value'=>'..',
     */

    public function fields(): array {
        return [
        ];
    }


    /**
     * Get the actions available for the resource.
     *
     *
     * @return array
     */
    public function actions() {
        return [
            new Actions\ArtisanAction(request()->input('cmd')),
            new Actions\TestAction,
        ];
    }
}
