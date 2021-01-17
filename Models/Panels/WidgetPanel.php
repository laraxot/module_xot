<?php

namespace Modules\Xot\Models\Panels;

//--- Services --

/**
 * Class WidgetPanel
 * @package Modules\Xot\Models\Panels
 */
class WidgetPanel extends XotBasePanel {
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = 'Modules\Xot\Models\Widget';

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
                'name' => 'layout_position',
                'comment' => null,
            ],
            (object) [
                'type' => 'String',
                'name' => 'post_type',
                'comment' => null,
            ],
            (object) [
                'type' => 'String',
                'name' => 'post_id',
                'comment' => null,
            ],
            (object) [
                'type' => 'String',
                'name' => 'title',
                'comment' => null,
            ],
            (object) [
                'type' => 'String',
                'name' => 'blade',
                'comment' => null,
            ],
            (object) [
                'type' => 'Image',
                'name' => 'image_src',
                'comment' => null,
            ],
            (object) [
                'type' => 'Integer',
                'name' => 'pos',
                'comment' => null,
            ],
            (object) [
                'type' => 'String',
                'name' => 'model',
                'comment' => null,
            ],
            (object) [
                'type' => 'String',
                'name' => 'limit',
                'comment' => null,
            ],
            (object) [
                'type' => 'String',
                'name' => 'order_by',
                'comment' => null,
            ],
        ];
    }
}
