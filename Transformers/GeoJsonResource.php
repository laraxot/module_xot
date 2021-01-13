<?php

declare(strict_types=1);

namespace Modules\Xot\Transformers;

/*
*  GEOJSON e' uno standard
* https://it.wikipedia.org/wiki/GeoJSON
**/

use Illuminate\Http\Resources\Json\JsonResource as ResCollection;
use Modules\Xot\Services\PanelService as Panel;

/**
 * Class GeoJsonResource.
 */
class GeoJsonResource extends ResCollection {
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \ReflectionException
     *
     * @return array
     */
    public function toArray($request) {
        $lang = app()->getLocale();
        //34     Parameter #1 $model of static method Modules\Xot\Services\PanelService::get() expects Illuminate\Database\Eloquent\Model, $this(Modules\Xot\Transformers\GeoJsonResource) given.
        return [
            'type' => 'Feature',
            'properties' => [
                'id' => $this->post_type.'-'.$this->post_id,
                //"index"=> 0,
                'isActive' => true,
                //"logo"=> "http://placehold.it/32x32",
                //'image' => Panel::get($this)->imgSrc(['width' => 200, 'height' => 200]),
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
