<?php

namespace Modules\Xot\Transformers;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class MapCollection
 * @package Modules\Xot\Transformers
 */
class MapCollection extends ResourceCollection {
    /**
     * @var string
     */
    public string $collects = MapResource::class;

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray(\Illuminate\Http\Request $request) {
        return [
            'type' => 'FeatureCollection',
            'features' => $this->collection,
            /*'links' => [
                'self' => 'link-value',
            ],*/
        ];
    }
}
