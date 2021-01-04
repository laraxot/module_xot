<?php

if (! isset($route_params)) {
    $route_params = [];
}

$ris = [
    0 => [
        (object) [
            'id' => '1',
            'nome' => 'Metatag',
            'visibility' => '1',
            'active' => 0,
            'url' => '#',
        ],
    ],

    1 => [
        (object) [
            'id' => '11',
            'nome' => 'Lista',
            'visibility' => '1',
            'active' => 0,
            'routename' => '',
            'url' => route('admin.container0.index',
                array_merge($route_params, ['container0' => 'metatag', 'lang' => 'it'])
            ),
        ],
    ],
];

return $ris;
