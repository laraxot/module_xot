<?php

namespace Modules\Xot\Models\Panels;

use Illuminate\Http\Request;

//--- Services --

/**
 * Class MetatagPanel
 * @package Modules\Xot\Models\Panels
 */
class MetatagPanel extends XotBasePanel {
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = 'Modules\Xot\Models\Metatag';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static string $title = 'title';

    /**
     * @return object[]
     */
    public function fields(): array {
        return [
            (object) [
                'type' => 'Id',
                'name' => 'id',
                'comment' => null,
            ],

            (object) [
                'type' => 'String',
                'name' => 'title',
                'comment' => null,
            ],

            (object) [
                'type' => 'Text',
                'name' => 'subtitle',
                'comment' => null,
            ],

            (object) [
                'type' => 'String',
                'name' => 'charset',
                'comment' => null,
            ],

            (object) [
                'type' => 'String',
                'name' => 'author',
                'comment' => null,
            ],

            (object) [
                'type' => 'Text',
                'name' => 'meta_description',
                'comment' => null,
            ],

            (object) [
                'type' => 'Text',
                'name' => 'meta_keywords',
                'comment' => null,
            ],

            (object) [
                'type' => 'Image',
                'name' => 'logo_src',
                'comment' => null,
            ],

            (object) [
                'type' => 'Text',
                'name' => 'logo_footer_src',
                'comment' => null,
            ],

            (object) [
                'type' => 'String',
                'name' => 'tennant_name',
                'comment' => null,
            ],
            (object) [
                'type' => 'String',
                'name' => 'sitename',
                'comment' => null,
            ],
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param Request|null $request
     * @return array
     */
    public function actions(Request $request = null) {
        return [
            new Actions\StoreFileMetatagAction(),
        ];
    }
}
