<?php

namespace Modules\Xot\Transformers;

//use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class MapResource extends JsonResource {
    public function toArray($request) {
        return [
            'type' => 'Feature',
            'properties' => [
                'p' => 'vending_machine',
                'id' => 'node/31605830',
            ],
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [round($this->longitude, 7), round($this->latitude, 7)],
            ],
        ];
    }
}

/*
{"type":"Feature","properties":{"p":"vending_machine","id":"node/31605830"},"geometry":{"type":"Point","coordinates":[9.0796524,48.5308688]
*/
