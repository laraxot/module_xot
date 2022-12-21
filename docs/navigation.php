<?php

declare(strict_types=1);

return [
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
];