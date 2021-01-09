<?php

namespace Modules\Xot\Transformers;

/*
*  GEOJSON e' uno standard
* https://it.wikipedia.org/wiki/GeoJSON
**/

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class GeoJsonCollection
 * @package Modules\Xot\Transformers
 */
class GeoJsonCollection extends ResourceCollection {
    /**
     * @var string
     */
    public $collects = GeoJsonResource::class;

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
