<?php

namespace Modules\Xot\Transformers;

/*
*  GEOJSON e' uno standard
* https://it.wikipedia.org/wiki/GeoJSON
**/

use Illuminate\Http\Resources\Json\JsonResource as ResCollection;
use Modules\Xot\Services\PanelService as Panel;

class GeoJsonResource extends ResCollection {
    public function toArray($request) {
        $lang = app()->getLocale();

        return [
            'type' => 'Feature',
            'properties' => [
                'id' => $this->post_type.'-'.$this->post_id,
                //"index"=> 0,
                'isActive' => true,
                //"logo"=> "http://placehold.it/32x32",
                'image' => Panel::get($this)->imgSrc(['width' => 200, 'height' => 200]),
                'link' => $this->url,
                'url' => '#',
                'name' => $this->title,
                'category' => $this->post_type,
                'email' => $this->email,
                'stars' => $this->ratings_avg,
                'phone' => $this->phone,
                'address' => $this->full_address,
                'about' => $this->subtitle."\r\n",
                'tags' => [
                    $this->post_type,
                    //"Restaurant",
                    //"Contemporary"
                ],
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
