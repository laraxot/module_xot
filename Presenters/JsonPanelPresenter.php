<?php

namespace Modules\Xot\Presenters;

use Illuminate\Support\Collection;
use Modules\Xot\Contracts\PanelPresenterContract;
use Modules\Xot\Services\StubService;

/**
 * Class JsonPanelPresenter
 * @package Modules\Xot\Presenters
 */
class JsonPanelPresenter implements PanelPresenterContract {
    /**
     * @var
     */
    protected $panel;

    /**
     * @param \Modules\Xot\Contracts\PanelContract $panel
     * @return mixed|void
     */
    public function setPanel(&$panel) {
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
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \ReflectionException
     */
    public function outContainer($params = null) {
        $model = $this->panel->row;
        $transformer = StubService::fromModel(['model' => $model, 'stub' => 'transformer_collection']);
        $rows = $this->panel->rows()->paginate(20);
        $out = new $transformer($rows);

        return $out;
    }

    /**
     * @param null $params
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \ReflectionException
     */
    public function outItem($params = null) {
        $model = $this->panel->row;
        $transformer = StubService::fromModel(['model' => $model, 'stub' => 'transformer_resource']);
        $out = new $transformer($model);

        return $out;
    }

    /**
     * @param null $params
     * @return mixed
     */
    public function out($params = null) {
        if (isContainer()) {
            return $this->outContainer($params);
        }

        return $this->outItem($params);
    }
}
