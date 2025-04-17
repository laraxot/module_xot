<?php

declare(strict_types=1);

return [
    /*
    'Getting Started' => [
        'url' => 'docs/getting-started',
        'children' => [
            'Customizing Your Site' => 'docs/customizing-your-site',
            'Navigation' => 'docs/navigation',
            'Algolia DocSearch' => 'docs/algolia-docsearch',
            'Custom 404 Page' => 'docs/custom-404-page',
        ],
    ],
    'Jigsaw Docs' => 'https://jigsaw.tighten.co/docs/installation',
    */
    'Base' => [
        'url' => 'docs/base',
        'children' => [
            'Installazione' => 'docs/base/installation',
            'Errori Comuni' => 'docs/base/issues',
            'Struttura' => 'docs/base/structure',
            'Url Not Found' => 'docs/base/url-not-found',
        ],
    ],
    'Config' => [
        'url' => 'docs/config',
        'children' => [
            'Configurazione modules.php' => 'docs/config/modules',
        ],
    ],
    'Model Actions' => [
        'url' => 'docs/model',
        'children' => [
            'Destroy' => 'docs/model/action/destroy',
            'Detach' => 'docs/model/action/detach',
            'Filter Relations' => 'docs/model/action/filter-relations',
            'Store' => 'docs/model/action/store',
            'Update' => 'docs/model/action/update',
        ],
    ],
    'Services' => [
        'url' => 'docs/service',
        'children' => [
            'Model' => 'docs/service/model',
            'Panel' => 'docs/service/panel',
            'Profile' => 'docs/service/profile',
        ],
    ],
    'Custom Relation' => 'docs/custom-relation',
    'Links' => [
        'url' => '#',
        'children' => [
            'Filter' => 'docs/links/filter',
        ],
    ],
    'Filament' => [
        'url' => 'docs/filament',
        'children' => [
            'Installazione' => 'docs/filament/installation',
            'Creazione di una Resource' => 'docs/filament/resource',
            'Creazioni Pdf da azione' => 'docs/filament/actions/pdf',
            'Ripristinare cartella vendor' => 'docs/filament/vendor',
        ],
    ],
    // 'Documentazione jigsaw' =>
];
