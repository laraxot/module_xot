<?php

namespace Modules\Xot\Transformers;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MapCollection extends ResourceCollection {
    public $collects = MapResource::class;

    public function toArray($request) {
        return [
            'type' => 'FeatureCollection',
            'features' => $this->collection,
            /*'links' => [
                'self' => 'link-value',
            ],*/
        ];
    }
}
