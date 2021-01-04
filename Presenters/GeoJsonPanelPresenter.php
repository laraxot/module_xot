<?php

namespace Modules\Xot\Presenters;

use Illuminate\Support\Collection;
use Modules\Blog\Models\Post;
use Modules\Xot\Contracts\PanelPresenterContract;
use Modules\Xot\Services\PanelService;

class GeoJsonPanelPresenter implements PanelPresenterContract {
    protected $panel;

    public function setPanel($panel) {
        $this->panel = $panel;
    }

    public function index(?Collection $items) {
    }

    public function outContainer($params = null) {
        $model = $this->panel->row;
        $model_table = $model->getTable();
        $model_type = PanelService::get($model)->postType();
        $transformer = \Modules\Geo\Transformers\GeoJsonCollection::class;
        //--------
        $lang = app()->getLocale();
        $rows = $this->panel->rows();
        $post_table = app(Post::class)->getTable();
        $rows = $rows->join($post_table.' as post',
            function ($join) use ($post_table,$lang,$model_table,$model_type) {
                $join->on('post.post_id', '=', $model_table.'.id')
                    ->select('title', 'guid', 'subtitle')
                    ->where('lang', $lang)
                    ->where('post.post_type', $model_type)
                ;
            }
            )
                    ->select('post.post_id', 'post_type', 'guid', 'latitude', 'longitude')
                    ->where('latitude', '!=', '')
                   // ->where('lang', $lang)
                    ->paginate(100)
                    ->appends(\Request::input());
        $out = new $transformer($rows);
        //--------

        return $out;
    }

    public function outItem($params = null) {
        $model = $this->panel->row;
        $transformer = \Modules\Geo\Transformers\GeoJsonResource::class;

        $out = new $transformer($model);

        return $out;
    }

    public function out($params = null) {
        if (isContainer()) {
            return $this->outContainer($params);
        }

        return $this->outItem($params);
    }
}
