<?php

namespace Modules\Xot\Services;

/**
 * Class PanelActionService
 * @package Modules\Xot\Services
 */
class PanelActionService {

    protected $panel;

    /**
     * PanelActionService constructor.
     * @param $panel
     */
    public function __construct(&$panel) {
        $this->panel = $panel;
    }

    /**
     * @param array $params
     * @return \Illuminate\Support\Collection
     */
    public function getActions($params = []) {
        $panel = $this->panel;

        extract($params);
        if (! isset($filters)) {
            $filters = [];
        }
        $actions = collect($panel->actions())->filter(
            function ($item) use ($filters) {
                $item->getName();
                $res = true;
                foreach ($filters as $k => $v) {
                    if (! isset($item->$k)) {
                        $item->$k = false;
                    }
                    if ($item->$k != $v) {
                        return false;
                    }
                }

                return $res;
            }
        )->map(
            function ($item) use ($panel) {
                $item->setPanel($panel);

                return $item;
            }
        );

        return $actions;
    }

    /**
     * @param array $params
     * @return \Illuminate\Support\Collection
     */
    public function containerActions($params = []) {
        $params['filters']['onContainer'] = true;

        return $this->getActions($params);
    }

    /**
     * @param array $params
     * @return \Illuminate\Support\Collection
     */
    public function itemActions($params = []) {
        $params['filters']['onItem'] = true;

        return $this->getActions($params);
    }

    /**
     * @param $act
     * @return mixed
     */
    public function itemAction($act) {
        $itemActions = $this->itemActions();
        $itemAction = $itemActions->firstWhere('name', $act);
        if (! is_object($itemAction)) {
            dddx([
                'error' => 'nessuna azione con questo nome',
                'act' => $act,
                'this' => $this,
                'itemActions' => $itemActions,
            ]);
        }
        //$itemAction->setPanel($this); //incerto dovrebbe farlo getActions

        return $itemAction;
    }

    /**
     * @param $act
     * @return mixed
     */
    public function containerAction($act) {
        $actions = $this->containerActions();
        $action = $actions->firstWhere('name', $act);
        if (! is_object($action)) {
            dddx([
                'error' => 'nessuna azione con questo nome',
                'act' => $act,
                'this' => $this,
                'Container Actions' => $actions,
                'panel' => $this->panel,
                'All Actions' => $this->panel->actions(),
            ]);
        }
        //$action->setPanel($this);

        return $action;
    }

    /**
     * @param $act
     * @return mixed
     */
    public function urlContainerAction($act) {
        $containerActions = $this->containerActions();
        $containerAction = $containerActions->firstWhere('name', $act);
        if (is_object($containerAction)) {
            return $containerAction->urlContainer(['rows' => $this->panel->rows, 'panel' => $this->panel]);
        }
    }

    /**
     * @param $act
     * @return mixed
     */
    public function urlItemAction($act) {
        $itemAction = $this->itemAction($act);
        if (is_object($itemAction)) {
            return $itemAction->urlItem(['row' => $this->panel->row, 'panel' => $this->panel]);
        }
    }

    /**
     * @param $act
     * @return mixed
     */
    public function btnItemAction($act) {
        $itemAction = $this->itemAction($act);
        if (is_object($itemAction)) {
            return $itemAction->btn(['row' => $this->panel->row, 'panel' => $this->panel]);
        }
    }
}
