<?php

declare(strict_types=1);

namespace Modules\Xot\Transformers;

/*
*  GEOJSON e' uno standard
* https://it.wikipedia.org/wiki/GeoJSON
**/

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class GeoJsonCollection.
 */
class GeoJsonCollection extends ResourceCollection
{
    /**
     * @var string
     */
    public $collects = GeoJsonResource::class;

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    // ErrorException (Declaration of Modules\Xot\Transformers\GeoJsonResource::toArray(Illuminate\Http\Request
    // $request) should be compatible with Illuminate\Http\Resources\Json\JsonResource::toArray($request)) thrown
    // while looking for class Modules\Xot\Transformers\GeoJsonResource.
    public function toArray($request)
    {
        return [
            'type' => 'FeatureCollection',
            'features' => $this->collection,
            /*'links' => [
                'self' => 'link-value',
            ],*/
        ];
    }
}
