<?php

namespace Modules\Xot\Presenters;

use Illuminate\Support\Collection;
use Modules\Blog\Models\Post;
use Modules\Xot\Contracts\PanelPresenterContract;
use Modules\Xot\Services\PanelService;

/**
 * Class GeoJsonPanelPresenter
 * @package Modules\Xot\Presenters
 */
class GeoJsonPanelPresenter implements PanelPresenterContract {

    protected $panel;

    /**
     * @param \Modules\Xot\Contracts\PanelContract $panel
     * @return mixed|void
     */
    public function setPanel(\Modules\Xot\Contracts\PanelContract &$panel) {
        $this->panel = $panel;
    }

    /**
     * @param Collection|null $items
     * @return mixed|void
     */
    public function index(?Collection $items) {
    }

    /**
     * @param null $params
     * @return \Modules\Xot\Transformers\GeoJsonCollection
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \ReflectionException
     */
    public function outContainer($params = null) {
        $model = $this->panel->row;
        $model_table = $model->getTable();
        $model_type = PanelService::get($model)->postType();
        $transformer = \Modules\Xot\Transformers\GeoJsonCollection::class;
        //--------

        $lang = app()->getLocale();
        $rows = $this->panel->rows();
        /*
        $post_table = app(Post::class)->getTable();
        $rows = $rows->join($post_table.' as post',
            function ($join) use ($lang,$model_table,$model_type) {
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
        */
        $rows = $rows->paginate(100);
        $out = new $transformer($rows);
        //--------

        return $out;
    }

    /**
     * @param null $params
     * @return \Modules\Xot\Transformers\GeoJsonResource
     */
    public function outItem($params = null) {
        $model = $this->panel->row;
        $transformer = \Modules\Xot\Transformers\GeoJsonResource::class;

        $out = new $transformer($model);

        return $out;
    }

    /**
     * @param null $params
     * @return \Modules\Xot\Transformers\GeoJsonCollection|\Modules\Xot\Transformers\GeoJsonResource
     */
    public function out($params = null) {
        if (isContainer()) {
            return $this->outContainer($params);
        }

        return $this->outItem($params);
    }
}
