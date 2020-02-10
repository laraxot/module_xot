<?php

$acts = [
    //['name' => 'attach'], //end act_n
    ['name' => 'detach', 'method' => ['DELETE', 'GET']], //end act_n
    //['name' => 'moveUp', 'method' => ['PUT', 'GET']],   // se uso "order" questi non mi servono
    //['name' => 'moveDown', 'method' => ['PUT', 'GET']],
]; //end acts

$cont_acts = [
    [
        'name' => 'Edit',
        'act' => 'indexEdit',
    ], //end act_n
    [
        'name' => 'Order',
        'act' => 'indexOrder',
    ], //end act_n
    [
        'name' => 'Attach',
        'act' => 'indexAttach',
        //'act'=>'attach',
    ], //end act_n
];

$item2 = [     //questo per avere /it/restaurant/ristotest/photo/edit
    'name' => '{container0}',
    'param_name' => 'item0',
    //'only'=>[],
    'subs' => [
        [
            'name' => '{container1}',
            'param_name' => '',
            'as' => 'container1.index_',
            'acts' => $cont_acts, //end acts
            //'only'=>[],
        ], //end sub_n
        [
            'name' => '{container1}',
            'param_name' => 'item1',
            'acts' => $acts,
            //'only'=>[],
            'subs' => [
                [
                    'name' => '{container2}',
                    'param_name' => '',
                    'as' => 'container2.index_',
                    'acts' => $cont_acts,
                    'only' => [],
                ], //end sub_n
                [
                    'name' => '{container2}',
                    'param_name' => 'item2',
                    'acts' => $acts,
                    //'only'=>[],
                    'subs' => [
                        [
                            'name' => '{container3}',
                            'param_name' => '',
                            'as' => 'container3.index_',
                            'acts' => $cont_acts,
                            'only' => [],
                        ], //end sub_n
                        [
                            'name' => '{container3}',
                            'param_name' => 'item3',
                            'acts' => $acts,
                        ], //end sub_n
                    ], //end subs
                ], //end sub_n
            ], //end subs
        ],
    ], //end subs
]; //end item2

$areas_prgs = [
    [
        'name' => '{container0}',
        'param_name' => '',
        'as' => 'container0.index_',
        'acts' => $cont_acts,
        /*[
                [
                    'name'=>'Edit',
                    'act'=>'indexEdit',
                ],
            ],*/
    ],
    $item2,
];

return $areas_prgs;
