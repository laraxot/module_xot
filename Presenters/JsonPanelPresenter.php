<?php

declare(strict_types=1);

namespace Modules\Xot\Presenters;

use Illuminate\Support\Collection;
use Modules\Xot\Contracts\PanelContract;
use Modules\Xot\Contracts\PanelPresenterContract;
use Modules\Xot\Services\StubService;

/**
 * Class JsonPanelPresenter.
 */
class JsonPanelPresenter implements PanelPresenterContract {
    protected PanelContract $panel;

    public function setPanel(PanelContract &$panel): self {
        $this->panel = $panel;

        return $this;
    }

    /**
     * @return mixed|void
     */
    public function index(?Collection $items) {
    }

    /**
     * @param null $params
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \ReflectionException
     *
     * @return mixed
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
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \ReflectionException
     *
     * @return mixed
     */
    public function outItem($params = null) {
        $model = $this->panel->row;
        $transformer = StubService::fromModel(['model' => $model, 'stub' => 'transformer_resource']);
        $out = new $transformer($model);

        return $out;
    }

    /**
     * @param null $params
     *
     * @return mixed
     */
    public function out($params = null) {
        if (isContainer()) {
            return $this->outContainer($params);
        }

        return $this->outItem($params);
    }
}
